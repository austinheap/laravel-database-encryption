<?php
/**
 * tests/Helper/HeaderPrefixTest.php
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
 * HeaderPrefixTest
 */
class HeaderPrefixTest extends TestCase
{
    public function testHeaderPrefixCached()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-CACHED-%s__');
        $helper = (new EncryptionHelper())->setHeaderPrefix(null);

        $this->assertAttributeEquals(null, 'prefixCache', $helper);
    }

    public function testHeaderPrefixValid()
    {
        $this->newRandom('__LARAVEL-DATABASE-ENCRYPTION-TEST-PREFIX-%s__');
        $helper = (new EncryptionHelper())->setHeaderPrefix($this->currentRandom());

        $this->assertEquals($this->currentRandom(), $helper->getHeaderPrefix());
    }

    public function testHeaderPrefixInvalid()
    {
        $prefix = '    ';
        $this->expectException(RuntimeException::class);
        (new EncryptionHelper())->setHeaderPrefix($prefix);
    }
}
