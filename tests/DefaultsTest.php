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
        $this->assertSame($defaults::DEFAULT_ENABLED, $defaults->isEnabledDefault());
        $this->assertSame(!$defaults::DEFAULT_ENABLED, $defaults->isDisabledDefault());
    }

    public function testVersioning()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertEquals(true, $defaults::DEFAULT_VERSIONING);
        $this->assertSame($defaults::DEFAULT_VERSIONING, $defaults->isVersioningDefault());
        $this->assertSame(!$defaults::DEFAULT_VERSIONING, $defaults->isVersionlessDefault());
    }

    public function testPrefix()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertEquals('__LARAVEL-DATABASE-ENCRYPTED-%VERSION%__', $defaults::DEFAULT_PREFIX);
        $this->assertSame($defaults::DEFAULT_PREFIX, $defaults->getPrefixDefault());
    }

    public function testHelpers()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $helpers  = [
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

        $this->assertTrue(method_exists($defaults, 'getHelpersDefault'));

        foreach ($helpers as $helper) {
            $this->assertContains($helper, $defaults::getHelpersDefault());
        }
    }

    public function testControlCharacters()
    {
        $defaults   = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $characters = [
            'header' => [
                'start' => 1,
                'stop'  => 4,
            ],
            'prefix' => [
                'start' => 2,
                'stop'  => 3,
            ],
            'type'   => [
                'start' => 30,
                'stop'  => 23,
            ],
        ];

        $this->assertTrue(method_exists($defaults, 'getControlCharactersDefault'));

        foreach ($characters as $key => $config) {
            $this->assertArrayHasKey($key, $defaults->getControlCharactersDefault());
        }
    }

    public function testControlCharactersCache()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();

        $this->assertClassHasAttribute('defaultControlCharactersCache', EncryptionDefaults::class);
        $this->assertAttributeEmpty('defaultControlCharactersCache', $defaults);
    }

    public function testPrefixCache()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();

        $this->assertClassHasAttribute('defaultPrefixCache', EncryptionDefaults::class);
        $this->assertAttributeEmpty('defaultPrefixCache', $defaults);
    }
}
