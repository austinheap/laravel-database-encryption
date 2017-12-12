<?php
/**
 * tests/DefaultsTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */

namespace AustinHeap\Database\Encryption\Tests;

use AustinHeap\Database\Encryption\EncryptionDefaults;

/**
 * DefaultsTest
 */
class DefaultsTest extends TestCase
{
    public function testEnabled()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertEquals(false, $defaults::DEFAULT_ENABLED);
    }

    public function testVersioning()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertEquals(true, $defaults::DEFAULT_VERSIONING);
    }

    public function testPrefix()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertEquals('__LARAVEL-DATABASE-ENCRYPTED-%VERSION%__', $defaults::DEFAULT_PREFIX);
    }

    public function testHelpers()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $helpers = [
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

        foreach ($helpers as $helper) {
            $this->assertContains($helper, $defaults::DEFAULT_HELPERS);
        }
    }

    public function testControlCharactersCache()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();

        $this->assertAttributeEmpty('defaultControlCharactersCache', $defaults);
    }

    public function testPrefixCache()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();

        $this->assertAttributeEmpty('defaultPrefixCache', $defaults);
    }
}
