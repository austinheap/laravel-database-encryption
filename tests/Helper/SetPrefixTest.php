<?php
/**
 * tests/Helper/SetPrefixTest.php
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
 * SetPrefixTest
 */
class SetPrefixTest extends TestCase
{
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
