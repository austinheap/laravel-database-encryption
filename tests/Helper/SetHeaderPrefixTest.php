<?php
/**
 * tests/Helper/SetHeaderPrefixTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */

namespace AustinHeap\Database\Encryption\Tests\Helper;

use AustinHeap\Database\Encryption\EncryptionHelper;
use AustinHeap\Database\Encryption\Tests\TestCase;
use DatabaseEncryption, RuntimeException;

/**
 * SetHeaderPrefixTest
 */
class SetHeaderPrefixTest extends TestCase
{
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
}
