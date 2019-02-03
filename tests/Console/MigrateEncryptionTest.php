<?php
/**
 * tests/Console/MigrateEncryptionTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.2.1
 */

namespace AustinHeap\Database\Encryption\Tests\Console;

use AustinHeap\Database\Encryption\Console\Commands\MigrateEncryptionCommand;
use AustinHeap\Database\Encryption\Tests\DatabaseTestCase;
use DatabaseEncryption;
use RuntimeException;

/**
 * MigrateEncryptionTest
 */
class MigrateEncryptionTest extends DatabaseTestCase
{
    public function testNotExtended()
    {
        $command = new MigrateEncryptionCommand();
        $this->expectException(RuntimeException::class);
        $command->handle();
    }

    public function testMissingOldKeys()
    {
        $command = new class() extends MigrateEncryptionCommand
        {
            protected function setupKeys()
            {
                $this->new_key = str_random(32);
                $this->tables  = ['test_models'];
            }
        };

        $this->expectException(RuntimeException::class);
        $command->handle();
    }

    public function testMissingNewKey()
    {
        $command = new class() extends MigrateEncryptionCommand
        {
            protected function setupKeys()
            {
                $this->old_keys = [app('config')->get('app.key')];
                $this->tables   = ['test_models'];
            }
        };

        $this->expectException(RuntimeException::class);
        $command->handle();
    }

    public function testMissingTables()
    {
        $command = new class() extends MigrateEncryptionCommand
        {
            protected function setupKeys()
            {
                $this->old_keys = [app('config')->get('app.key')];
                $this->new_key  = str_random(32);
            }
        };

        $this->expectException(RuntimeException::class);
        $command->handle();
    }

    public function testExtended()
    {
        $this->resetDatabase();

        $command = new class() extends MigrateEncryptionCommand
        {
            protected function setupKeys()
            {
                $this->old_keys = [app('config')->get('app.key')];
                $this->new_key  = str_random(32);
                $this->tables   = ['test_models'];
            }
        };

        $this->callProtectedMethod($command, 'setupKeys');
        $this->assertAttributeNotEmpty('new_key', $command);
        $this->assertAttributeEquals(['test_models'], 'tables', $command);

        $ids = $this->randomModels(LARAVEL_DATABASE_ENCRYPTION_ITERATIONS);
        $this->assertCount(LARAVEL_DATABASE_ENCRYPTION_ITERATIONS, $ids);

        $command->handle();

        $stats = $command::getStats();
        $this->assertNotNull($stats);
    }
}
