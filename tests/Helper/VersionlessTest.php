<?php
/**
 * tests/Helper/VersionlessTest.php
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Helper;

use AustinHeap\Database\Encryption\Tests\TestCase;
use AustinHeap\Database\Encryption\EncryptionHelper;

/**
 * VersionlessTest
 */
class VersionlessTest extends TestCase
{
    public function testVersionlessCached()
    {
        $helper = (new EncryptionHelper())->setVersionless(null);

        $this->assertAttributeEquals(null, 'versioningCache', $helper);
        $this->assertTrue(is_bool($helper->isVersionless()));
        $this->assertAttributeNotSame(null, 'versioningCache', $helper);
    }

    public function testVersionlessTrue()
    {
        $helper = (new EncryptionHelper())->setVersionless(true);

        $this->assertTrue($helper->isVersionless());
        $this->assertFalse($helper->isVersioning());
    }

    public function testVersionlessFalse()
    {
        $helper = (new EncryptionHelper())->setVersionless(false);

        $this->assertFalse($helper->isVersionless());
        $this->assertTrue($helper->isVersioning());
    }
}
