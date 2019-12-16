<?php
/**
 * tests/Defaults/HelpersTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Defaults;

use AustinHeap\Database\Encryption\Tests\TestCase;
use AustinHeap\Database\Encryption\EncryptionDefaults;

/**
 * HelpersTest
 */
class HelpersTest extends TestCase
{
    private static $helpers = [
        'database_encryption',
        'db_encryption',
        'dbencryption',
        'database_encrypt',
        'db_encrypt',
        'dbencrypt',
        'database_decrypt',
        'db_decrypt',
        'dbdecrypt',
    ];

    public function testHelpersGetter()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();

        $this->assertTrue(method_exists($defaults, 'getHelpersDefault'));
        $this->assertEquals(count(self::$helpers), count($defaults->getHelpersDefault()));
    }

    public function testHelpersContents()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();

        foreach (self::$helpers as $helper) {
            $this->assertContains($helper, $defaults::getHelpersDefault());
        }
    }

    public function testHelpersMethods()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();

        foreach (self::$helpers as $helper) {
            $this->assertTrue(function_exists($helper));
        }
    }
}
