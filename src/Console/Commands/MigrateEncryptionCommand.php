<?php
/**
 * src/Console/Commands/MigrateEncryptionCommand.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption\Console\Commands;

use AustinHeap\Database\Encryption\EncryptionFacade as DatabaseEncryption;
use Exception;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class MigrateEncryptionCommand.
 *
 * This console job locates data in the database that contains data encrypted
 * using a wrong/deprecated encryption key, and re-encrypts it using the
 * correct/new encryption key.
 *
 * It can be used to fix badly encrypted data, or can be used to decrypt data using
 * one key and re-encrypt using another key.
 *
 * ### Installation
 *
 * * Override this class and change the setupKeys() function to set the keys that
 *   are to be used ($old_keys and $new_key) as well as the array of $table names.
 *
 * * Add 'App\Console\Commands\MigrateEncryptionCommand' to the $commands array in
 *   your 'App\Console\Kernel' package.
 *
 * ### Example
 *
 * <code>
 * php artisan migrate:encryption
 * </code>
 */
class MigrateEncryptionCommand extends \Illuminate\Console\Command
{
    /**
     * The stats of the last run of the console command.
     *
     * @var array
     */
    private static $stats = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:encryption';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rotate keys used for database encryption';

    /**
     * An array of old keys.  Each one is to be tried in turn.
     *
     * @var array
     */
    protected $old_keys = null;

    /**
     * The new encryption key.
     *
     * @var string
     */
    protected $new_key = null;

    /**
     * The list of tables to be scanned.
     *
     * @var array
     */
    protected $tables = null;

    /**
     * Get the configuration setting for the prefix used to determine if a string is encrypted.
     *
     * @return string
     */
    protected function getEncryptionPrefix(): string
    {
        return DatabaseEncryption::getPrefix();
    }

    /**
     * Determine whether a string has already been encrypted.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isEncrypted($value): bool
    {
        return strpos((string) $value, $this->getEncryptionPrefix()) === 0;
    }

    /**
     * Return the encrypted value of an attribute's value.
     *
     * @param string    $value
     * @param Encrypter $cipher
     *
     * @return null|string
     */
    public function encryptedAttribute($value, $cipher): ?string
    {
        return $this->getEncryptionPrefix().$cipher->encrypt($value);
    }

    /**
     * Return the decrypted value of an attribute's encrypted value.
     *
     * @param string    $value
     * @param Encrypter $cipher
     *
     * @return null|string
     */
    public function decryptedAttribute($value, $cipher): ?string
    {
        return $cipher->decrypt(str_replace($this->getEncryptionPrefix(), '', $value));
    }

    /**
     * Set up keys.
     *
     * @return void
     */
    protected function setupKeys()
    {
        // Over-ride this function to set:
        //
        // * $this->old_keys
        // * $this->new_key
        // * $this->tables
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Keys
        $this->setupKeys();

        throw_if(! is_array($this->old_keys) || empty($this->old_keys) || count($this->old_keys) == 0,
                 RuntimeException::class,
                 'You must override this class with (array)$old_keys set correctly.');
        throw_if(! is_string($this->new_key) || empty($this->new_key), RuntimeException::class,
                 'You must override this class with (string)$new_key set correctly.');
        throw_if(! is_array($this->tables) || empty($this->tables) || count($this->tables) == 0, RuntimeException::class,
                 'You must override this class with (array)$tables set correctly.');

        // Encrypter objects
        $cipher = Config::get('app.cipher', 'AES-256-CBC');
        $base_encrypter = new Encrypter($this->new_key, $cipher);
        $old_encrypter = [];

        foreach ($this->old_keys as $key => $value) {
            $old_encrypter[$key] = new Encrypter($value, $cipher);
        }

        // Stats
        $stats = [
            'tables'     => count($this->tables),
            'rows'       => 0,
            'attributes' => 0,
            'failed'     => 0,
            'migrated'   => 0,
            'skipped'    => 0,
        ];

        // Main
        $this->writeln('<fg=green>Migrating <fg=blue>'.count($this->old_keys).'</> old database encryption key(s) on <fg=blue>'.$stats['tables'].'</> table(s).</>');

        foreach ($this->tables as $table_name) {
            // Process table
            $this->writeln('<fg=yellow>Fetching data from: <fg=white>"</><fg=green>'.$table_name.'<fg=white>"</>.</>');

            // Setup table stats
            $table_stats = ['rows' => 0, 'attributes' => 0, 'failed' => 0, 'migrated' => 0, 'skipped' => 0];

            // Get count of records
            $count = DB::table($table_name)
                       ->count();

            // Create progress bar
            $bar = defined('LARAVEL_DATABASE_ENCRYPTION_TESTS') ? null : $this->output->createProgressBar($count);

            $this->writeln('<fg=yellow>Found <fg=blue>'.number_format($count, 0).'</> record(s) in database; checking encryption keys.</>');

            // Get table object
            $table_data = DB::table($table_name)
                            ->orderBy('id');

            // Cycle through table data 1k records at a time
            $chunk = 1000;
            $table_data->chunk($chunk, function ($data) use (
                &$stats,
                &$table_stats,
                $bar,
                $chunk,
                $base_encrypter,
                $old_encrypter,
                $table_name
            ) {
                foreach ($data as $datum) {
                    // Check every column of the table for an encrypted value.  If the value is
                    // encrypted then try to decrypt it with the base encrypter.
                    $datum_array = get_object_vars($datum);
                    $adjust = [];

                    $table_stats['rows'] += 1;

                    foreach ($datum_array as $key => $value) {
                        $table_stats['attributes'] += 1;

                        if (! $this->isEncrypted($value)) {
                            $table_stats['skipped'] += 1;
                            continue;
                        }

                        try {
                            $test = $this->decryptedAttribute($value, $base_encrypter);
                            continue;
                        } catch (Exception $e) {

                            // If the base encrypter fails then try to decrypt it with each
                            // other encrypter until one works or they all fail.
                            $new_value = '';

                            foreach ($old_encrypter as $cipher) {
                                try {
                                    $test = $this->decryptedAttribute($value, $cipher);

                                    // If that did not throw an exception then we have a match
                                    // between the old encrypter and the encrypted value, so
                                    // adjust the new value.
                                    $new_value = $this->encryptedAttribute($test, $base_encrypter);
                                    continue;
                                } catch (\Exception $e) {
                                    // Do nothing, keep trying.
                                }
                            }

                            // If we got a match then empty($new_value) != true
                            if (empty($new_value)) {
                                Log::error(
                                    __CLASS__.':'.__TRAIT__.':'.__FILE__.':'.__LINE__.':'.__FUNCTION__.':'.
                                    'Unable to find encryption key for: '.$table_name->key.' #'.$datum->id
                                );

                                $table_stats['failed'] += 1;
                                continue;
                            }

                            // We got a match
                            $adjust[$key] = $new_value;
                            $table_stats['migrated'] += 1;
                        }

                        $table_stats['attributes'] += 1;
                    }

                    // If we have anything in $adjust, write that back to the database
                    if (count($adjust) == 0) {
                        continue;
                    }

                    DB::table($table_name)
                      ->where('id', '=', $datum->id)
                      ->update($adjust);
                }

                // Advance progress bar
                if (! defined('LARAVEL_DATABASE_ENCRYPTION_TESTS')) {
                    $bar->advance($chunk);
                }
            });

            // Finish progress bar
            if (! defined('LARAVEL_DATABASE_ENCRYPTION_TESTS')) {
                $bar->finish();
            }

            // And display stats
            foreach ($table_stats as $key => $value) {
                $stats[$key] += $value;
            }

            $this->writeln('');
            $this->writeln('<fg=blue>Database encryption migration for table <fg=white>"</><fg=green>'.$table_name.'</><fg=white>"</> complete: '.self::buildStatsString($table_stats).'.</>');
        }

        $this->writeln('<fg=green>Database encryption migration for all <fg=blue>'.$stats['tables'].'</> table(s) complete: '.self::buildStatsString($stats).'.</>');
        self::setStats($stats);
    }

    private function writeln(string $line): void
    {
        $output = $this->getOutput();

        if (! is_null($output)) {
            $output->writeln($line);
        }
    }

    private static function buildStatsString(array $stats, string $stat = null, bool $stylize = true): string
    {
        $string = '';

        foreach ($stats as $key => $value) {
            if (! is_null($stat) && $key != $stat) {
                continue;
            }

            $string .= self::stylizeStatsString($key, 'fg=white', $stylize).
                       self::stylizeStatsString(' = ', 'fg=yellow', $stylize).
                       self::stylizeStatsString(is_int($value) ? number_format($value, 0) : $value, 'fg=magenta',
                                                $stylize).'; ';
        }

        return empty($string) ? '' : substr($string, 0, -2);
    }

    private static function stylizeStatsString(string $string, string $style, bool $stylize = true): string
    {
        return ! $stylize ? $string : '<'.$style.'>'.$string.'</'.(strpos($style,
                                                                                   '<fg') === 0 ? '' : $style).'>';
    }

    private static function setStats(array $stats): void
    {
        self::$stats = $stats;
    }

    public static function getStats(): array
    {
        throw_if(is_null(self::$stats), RuntimeException::class, 'Stats do not exist; command has not been executed.');

        return self::$stats;
    }
}
