<?php
/**
 * Class ReEncrypt.
 *
 * @author del
 */
namespace AustinHeap\Database\Encryption\Console;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class MigrateEncryptionCommand.
 *
 * This console job locates data in the database that contains data encrypted
 * using a wrong/deprecated encryption key, and re-encrypts it using the correct/
 * new encryption key.
 *
 * It can be used to fix badly encrypted data, or can be used to decrypt data using
 * one key and re-encrypt using another key.
 *
 * ### Installation
 *
 * * Over-ride this class and change the setupKeys() function to set the keys that
 *   are to be used (old_keys and new_keys) as well as the table names.
 * * Add 'App\Console\Commands\MigrateEncryptionCommand' to the $commands array in
 *   your 'App\Console\Kernel' package.
 *
 * ### Example
 *
 * <code>
 * php artisan migrate:encryption
 * </code>
 */
class MigrateEncryptionCommand extends Command
{
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
    protected $description = 'Re-encrypt data using wrong encryption keys.';

    /**
     * An array of old keys.  Each one is to be tried in turn.
     *
     * @var array
     */
    protected $old_keys = [];

    /**
     * The new encryption key.
     *
     * @var string
     */
    protected $new_key = '';

    /**
     * The list of tables to be scanned.
     *
     * @var array
     */
    protected $tables = [];

    /**
     * Get the configuration setting for the prefix used to determine if a string is encrypted.
     *
     * @return string
     */
    protected function getElocryptPrefix()
    {
        return Config::has('elocrypt.prefix') ? Config::get('elocrypt.prefix') : '__ELOCRYPT__:';
    }

    /**
     * Determine whether a string has already been encrypted.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isEncrypted($value)
    {
        return strpos((string) $value, $this->getElocryptPrefix()) === 0;
    }

    /**
     * Return the encrypted value of an attribute's value.
     *
     * @param string    $value
     * @param Encrypter $cipher
     *
     * @return string
     */
    public function encryptedAttribute($value, $cipher)
    {
        return $this->getElocryptPrefix() . $cipher->encrypt($value);
    }

    /**
     * Return the decrypted value of an attribute's encrypted value.
     *
     * @param string    $value
     * @param Encrypter $cipher
     *
     * @return string
     */
    public function decryptedAttribute($value, $cipher)
    {
        return $cipher->decrypt(str_replace($this->getElocryptPrefix(), '', $value));
    }

    /**
     * Set up keys.
     *
     * @return void
     */
    protected function setupKeys()
    {
        // Over-ride this function to set:
        // * $this->old_keys
        // * $this->new_keys
        // * $this->tables
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Set up the keys
        $this->setupKeys();

        // Check that the keys hve been set up
        if (empty($this->old_keys) || count($this->old_keys) == 0) {
            $this->comment('Please over-ride this class with old_keys set correctly');
            exit(1);
        }

        if (empty($this->new_key)) {
            $this->comment('Please over-ride this class with old_keys set correctly');
            exit(1);
        }

        if (empty($this->tables) || count($this->tables) == 0) {
            $this->comment('Please over-ride this class with tables set correctly');
            exit(1);
        }

        // Make some encrypter objects
        $cipher        = Config::get('app.cipher');
        $baseEncrypter = new Encrypter($this->new_key, $cipher);
        $oldEncrypter  = [];
        foreach ($this->old_keys as $key => $value) {
            $oldEncrypter[$key] = new Encrypter($value, $cipher);
        }

        foreach ($this->tables as $table_name) {
            $this->comment("Fetching data from $table_name");

            // Get count of records
            $count = DB::table($table_name)
                ->count();

            // Create a progress bar
            $bar = $this->output->createProgressBar($count);

            $count = number_format($count, 0, '.', ',');
            $this->comment('Found ' . $count . ' records in DB. Checking encryption keys.');

            // Get a table object
            $table_data = DB::table($table_name);

            // Cycle through the table data 1000 records at a time
            $chunk = 1000;
            $table_data->chunk($chunk, function ($data) use ($bar, $chunk, $baseEncrypter, $oldEncrypter, $table_name) {
                foreach ($data as $datum) {
                    $datum_array = get_object_vars($datum);

                    // Check every column of the table for an encrypted value.  If the value is
                    // encrypted then try to decrypt it with the base encrypter.
                    $adjust = [];
                    foreach ($datum_array as $key => $value) {
                        if (! $this->isEncrypted($value)) {
                            continue;
                        }

                        try {
                            $test = $this->decryptedAttribute($value, $baseEncrypter);
                            continue;
                        } catch (\Exception $e) {

                            // If the base encrypter fails then try to decrypt it with each
                            // other encrypter until one works or they all fail.

                            $new_value = '';
                            foreach ($oldEncrypter as $cipher) {
                                try {
                                    $test = $this->decryptedAttribute($value, $cipher);

                                    // If that did not throw an exception then we have a match
                                    // between the old encrypter and the encrypted value, so
                                    // adjust the new value.
                                    $new_value = $this->encryptedAttribute($test, $baseEncrypter);
                                    continue;
                                } catch (\Exception $e) {
                                    // Do nothing, keep trying.
                                }
                            }

                            // If we got a match then we will have something in $new_value
                            if (empty($new_value)) {
                                Log::error(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
                                    "Unable to find an encryption key to match for ${table_name}.${key} ID " . $datum->id
                                );
                                continue;
                            }

                            // We got a match
                            $adjust[$key] = $new_value;
                        }
                    }

                    // Now if we have anything in $adjust we can write that back to the database
                    if (count($adjust) == 0) {
                        continue;
                    }

                    DB::table($table_name)
                        ->where('id', '=', $datum->id)
                        ->update($adjust);
                }

                // Advance the stick along one.
                $bar->advance($chunk);
            });

            // After each table, finish the progress bar.
            $bar->finish();
            $this->comment('');
        }

        $this->comment('All tables complete');
    }
}
