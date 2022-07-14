<?php
/**
 * tests/Helper/PrefixTest.php
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Helper;

use AustinHeap\Database\Encryption\EncryptionHelper;
use AustinHeap\Database\Encryption\Tests\TestCase;
use DatabaseEncryption, RuntimeException;

/**
 * PrefixTest
 */
class PrefixTest extends TestCase
{
    public function testPrefixCached()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-CACHED-%s__');
        $helper = (new EncryptionHelper())->setPrefix(null);

        $this->assertAttributeEquals(null, 'prefixCache', $helper);

        $helper->setPrefix($this->currentRandom());

        $this->assertEquals($this->currentRandom(), $helper->getPrefix());
        $this->assertAttributeSame($this->currentRandom(), 'prefixCache', $helper);
    }

    public function testPrefixValid()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-%s__');
        $helper = (new EncryptionHelper())->setPrefix($this->currentRandom());

        $this->assertEquals($this->currentRandom(), $helper->getPrefix());
    }

    public function testPrefixInvalid()
    {
        $prefix = '    ';
        $this->expectException(RuntimeException::class);
        (new EncryptionHelper())->setPrefix($prefix);
    }

    public function testPrefixVersioned()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-%s-%%VERSION%%__');
        $helper = (new EncryptionHelper())->setPrefix($this->currentRandom());

        $this->assertEquals(str_replace('%VERSION%', 'VERSION-00-01-02', $this->currentRandom()), $helper->getPrefix());
    }

    public function testPrefixVersionless()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-%s-%%VERSION%%__');
        $helper = (new EncryptionHelper())->setVersionless(true)->setPrefix($this->currentRandom());

        $this->assertEquals($this->currentRandom(), $helper->getPrefix());
    }
}
