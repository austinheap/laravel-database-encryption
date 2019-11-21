<?php
/**
 * tests/DatabaseTestCase.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.2.1
 */

namespace AustinHeap\Database\Encryption\Tests;

use AustinHeap\Database\Encryption\Tests\Models\DatabaseModel;
use DB, RuntimeException;

/**
 * DatabaseTestCase
 */
class DatabaseTestCase extends TestCase
{
    private static $database = null;

    private $last_random_strings = null;

    public function tearDown(): void
    {
        $this->tearDownDatabase();
        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app): void
    {
        if (is_null(self::$database)) {
            self::$database = 'laravel_database_encryption_testing_' . str_random(6);
        }

        $this->setUpDatabase();

        $app['config']->set('database.connections.testing', [
            'driver'      => 'mysql',
            'host'        => env('TESTING_DB_HOST', '127.0.0.1'),
            'port'        => env('TESTING_DB_PORT', 3306),
            'database'    => self::$database,
            'username'    => env('TESTING_DB_USER', 'root'),
            'password'    => env('TESTING_DB_PASS', ''),
            'unix_socket' => '',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ]);

        $app['config']->set('database.default', 'testing');

        parent::getEnvironmentSetUp($app);
    }

    public function setUpDatabase(): void
    {
        throw_if(is_null(self::$database), RuntimeException::class, 'Testing database not set.');

        $this->tearDownDatabase();
        $this->runStatement('CREATE DATABASE ' . self::$database . ';');

        foreach (['migrations', 'users', 'password_resets', 'test_models'] as $name) {
            $this->runStatement($name . '.sql', self::$database);
        }
    }

    public function resetDatabase(): void
    {
        $this->tearDownDatabase();
        $this->setUpDatabase();
    }

    public function tearDownDatabase(): void
    {
        if (is_null(self::$database)) {
            return;
        }

        $this->runStatement('DROP DATABASE IF EXISTS ' . self::$database . ';');
    }

    protected function runStatement(string $statement, ?string $database = null): void
    {
        if (is_file(__DIR__ . '/Database/' . $statement)) {
            $statement = file_get_contents(__DIR__ . '/Database/' . $statement);
        }

        $id = uniqid();
        $file = __DIR__ . '/testing-'.$id.'.sql';
        file_put_contents($file, is_null($database) ? $statement : 'USE ' . $database . '; ' . $statement);
        $cmd = 'mysql -u' . env('TESTING_DB_USER', 'root') .
               (empty(env('TESTING_DB_PASS', '')) ? '' : ' -p' . env('TESTING_DB_PASS', '')) .
               ' -h ' . env('TESTING_DB_HOST', '127.0.0.1') .
               ' < ' . $file . ' 2>&1 | grep -v "Warning: Using a password"';
        exec($cmd);
        unlink($file);
    }

    protected function randomModels(int $count): array
    {
        $ids = [];

        for ($x = 0; $x < $count; $x++) {
            $model = DatabaseModel::create($this->randomValues());
            $ids[] = $model->id;
        }

        return $ids;
    }

    protected function randomValues(): array
    {
        $this->last_random_strings = [
            'should_be_encrypted' => $this->newRandom('test-value-that-should-be-encrypted-%s'),
            'shouldnt_be_encrypted' => $this->newRandom('test-value-that-should-not-be-encrypted-%s'),
            'should_be_encrypted_float' => (float) rand(1, 9999) / 1000,
            'shouldnt_be_encrypted_float' => (float) rand(1, 9999) / 1000,
            'should_be_encrypted_int' => (int) rand(1, 9999),
            'shouldnt_be_encrypted_int' => (int) rand(1, 9999),
        ];

        return $this->last_random_strings;
    }
}
