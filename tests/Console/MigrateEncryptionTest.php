<?php
/**
 * tests/Console/MigrateEncryptionTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.1.0
 */

namespace AustinHeap\Database\Encryption\Tests\Console;

use AustinHeap\Database\Encryption\Console\Commands\MigrateEncryptionCommand;
use AustinHeap\Database\Encryption\Tests\TestCase;
use DatabaseEncryption;
use RuntimeException;

/**
 * MigrateEncryptionTest
 */
class MigrateEncryptionTest extends TestCase
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
                $this->new_keys = [str_random(32)];
                $this->tables   = ['test_models'];
            }
        };

        $this->expectException(RuntimeException::class);
        $command->handle();
    }

    public function testMissingNewKeys()
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
                $this->new_keys = [str_random(32)];
            }
        };

        $this->expectException(RuntimeException::class);
        $command->handle();
    }

//    public function testValid()
//    {
//        $command = new class() extends MigrateEncryptionCommand
//        {
//            protected function setupKeys()
//            {
//                $this->old_keys = [app('config')->get('app.key')];
//                $this->new_keys = [str_random(32)];
//                $this->tables   = ['test_models'];
//            }
//        };
//        $this->callProtectedMethod($command, 'setupKeys');
//
//        $command->handle();
//        $this->assertTrue(true);
//    }
}