<?php
/**
 * tests/Defaults/PrefixTest.php
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
 * PrefixTest
 */
class PrefixTest extends TestCase
{
    public function testPrefix()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertEquals('__LARAVEL-DATABASE-ENCRYPTED-%VERSION%__', $defaults::DEFAULT_PREFIX);
        $this->assertSame($defaults::DEFAULT_PREFIX, $defaults->getPrefixDefault());
    }
}
