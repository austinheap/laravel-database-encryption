<?php
/**
 * tests/HelperTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */

namespace AustinHeap\Database\Encryption\Tests;

use AustinHeap\Database\Encryption\EncryptionHelper;
use DatabaseEncryption, RuntimeException;

/**
 * HelperTest
 */
class HelperTest extends TestCase
{
    public function testSingleton()
    {
        $this->assertSame(DatabaseEncryption::getInstance(), EncryptionHelper::getInstance());
    }

    public function testSetEnabledCached()
    {
        $helper = (new EncryptionHelper())->setEnabled(null);

        $this->assertAttributeEquals(null, 'enabledCache', $helper);
        $this->assertTrue(is_bool($helper->isEnabled()));
        $this->assertAttributeNotSame(null, 'enabledCache', $helper);
    }

    public function testSetEnabledTrue()
    {
        $helper = (new EncryptionHelper())->setEnabled(true);

        $this->assertTrue($helper->isEnabled());
    }

    public function testSetEnabledFalse()
    {
        $helper = new EncryptionHelper();

        $helper->setEnabled(false);
        $this->assertFalse($helper->isEnabled());
    }

    public function testSetDisabledCached()
    {
        $helper = (new EncryptionHelper())->setDisabled(null);

        $this->assertAttributeEquals(null, 'enabledCache', $helper);
        $this->assertTrue(is_bool($helper->isDisabled()));
        $this->assertAttributeNotSame(null, 'enabledCache', $helper);
    }

    public function testSetDisabledTrue()
    {
        $helper = (new EncryptionHelper())->setDisabled(true);

        $this->assertTrue($helper->isDisabled());
        $this->assertFalse($helper->isEnabled());
    }

    public function testSetDisabledFalse()
    {
        $helper = (new EncryptionHelper())->setDisabled(false);

        $this->assertFalse($helper->isDisabled());
        $this->assertTrue($helper->isEnabled());
    }

    public function testSetVersioningCached()
    {
        $helper = (new EncryptionHelper())->setVersioning(null);

        $this->assertAttributeEquals(null, 'versioningCache', $helper);
        $this->assertTrue(is_bool($helper->isVersioning()));
        $this->assertAttributeNotSame(null, 'versioningCache', $helper);
    }

    public function testSetVersioningTrue()
    {
        $helper = (new EncryptionHelper())->setVersioning(true);

        $this->assertTrue($helper->isVersioning());
    }

    public function testSetVersioningFalse()
    {
        $helper = new EncryptionHelper();

        $helper->setVersioning(false);
        $this->assertFalse($helper->isVersioning());
    }

    public function testSetVersionlessCached()
    {
        $helper = (new EncryptionHelper())->setVersionless(null);

        $this->assertAttributeEquals(null, 'versioningCache', $helper);
        $this->assertTrue(is_bool($helper->isVersionless()));
        $this->assertAttributeNotSame(null, 'versioningCache', $helper);
    }

    public function testSetVersionlessTrue()
    {
        $helper = (new EncryptionHelper())->setVersionless(true);

        $this->assertTrue($helper->isVersionless());
        $this->assertFalse($helper->isVersioning());
    }

    public function testSetVersionlessFalse()
    {
        $helper = (new EncryptionHelper())->setVersionless(false);

        $this->assertFalse($helper->isVersionless());
        $this->assertTrue($helper->isVersioning());
    }




    public function testSetHeaderPrefixCached()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-CACHED-%s__');
        $helper = (new EncryptionHelper())->setHeaderPrefix(null);

        $this->assertAttributeEquals(null, 'prefixCache', $helper);

//        $helper->setHeaderPrefix($this->currentRandom());
//dump($helper->getHeaderPrefix());
//        $this->assertEquals($this->currentRandom(), $helper->getHeaderPrefix());
//        $this->assertAttributeSame($this->currentRandom(), 'prefixCache', $helper);
    }

    public function testSetHeaderPrefixValid()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-%s__');
        $helper = (new EncryptionHelper())->setHeaderPrefix($this->currentRandom());

        $this->assertEquals($this->currentRandom(), $helper->getHeaderPrefix());
    }

    public function testSetHeaderPrefixInvalid()
    {
        $prefix = '    ';
        $this->expectException(RuntimeException::class);
        (new EncryptionHelper())->setHeaderPrefix($prefix);
    }



    public function testSetPrefixCached()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-CACHED-%s__');
        $helper = (new EncryptionHelper())->setPrefix(null);

        $this->assertAttributeEquals(null, 'prefixCache', $helper);

        $helper->setPrefix($this->currentRandom());

        $this->assertEquals($this->currentRandom(), $helper->getPrefix());
        $this->assertAttributeSame($this->currentRandom(), 'prefixCache', $helper);
    }

    public function testSetPrefixValid()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-%s__');
        $helper = (new EncryptionHelper())->setPrefix($this->currentRandom());

        $this->assertEquals($this->currentRandom(), $helper->getPrefix());
    }

    public function testSetPrefixInvalid()
    {
        $prefix = '    ';
        $this->expectException(RuntimeException::class);
        (new EncryptionHelper())->setPrefix($prefix);
    }
}
