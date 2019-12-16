<?php
/**
 * tests/Helper/VersioningTestp
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Helper;

use AustinHeap\Database\Encryption\Tests\TestCase;
use AustinHeap\Database\Encryption\EncryptionHelper;

/**
 * VersioningTest
 */
class VersioningTest extends TestCase
{
    public function testVersioningCached()
    {
        $helper = (new EncryptionHelper())->setVersioning(null);

        $this->assertAttributeEquals(null, 'versioningCache', $helper);
        $this->assertTrue(is_bool($helper->isVersioning()));
        $this->assertAttributeNotSame(null, 'versioningCache', $helper);
    }

    public function testVersioningTrue()
    {
        $helper = (new EncryptionHelper())->setVersioning(true);

        $this->assertTrue($helper->isVersioning());
    }

    public function testVersioningFalse()
    {
        $helper = new EncryptionHelper();

        $helper->setVersioning(false);
        $this->assertFalse($helper->isVersioning());
    }
}
