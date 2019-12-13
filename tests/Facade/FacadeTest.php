<?php
/**
 * tests/FacadeTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests;

use RuntimeException;
use AustinHeap\Database\Encryption\EncryptionFacade;
use AustinHeap\Database\Encryption\EncryptionHelper;
use DatabaseEncryption as EncryptionRealFacade;

/**
 * FacadeTest
 */
class FacadeTest extends TestCase
{
    public function testManualConstruct()
    {
        $facade = new EncryptionFacade();
        $this->assertEquals('DatabaseEncryption', $this->callProtectedMethod($facade, 'getFacadeAccessor'));
    }

    public function testConstructValid()
    {
        $this->assertEquals(app('DatabaseEncryption'), database_encryption());
    }

    public function testConstructInvalid()
    {
        $helper = new class() extends EncryptionHelper {};
        $this->assertNotEquals(app('DatabaseEncryption'), $helper);
    }

    public function testAccessor()
    {
        $this->assertEquals(self::callProtectedMethod(new EncryptionFacade(), 'getFacadeAccessor'), 'DatabaseEncryption');
    }

    public function testFacade()
    {
        $this->assertSame(app('DatabaseEncryption'), EncryptionRealFacade::getInstance());
        $this->assertEquals(EncryptionHelper::VERSION, EncryptionRealFacade::getVersion());
    }

    public function testCallStaticInvalid()
    {
        $class = new class() extends EncryptionFacade
        {
            public static function getFacadeRoot()
            {
                return null;
            }
        };

        $this->expectException(RuntimeException::class);
        $class::staticMethodThatDoesntExist();
    }
}
