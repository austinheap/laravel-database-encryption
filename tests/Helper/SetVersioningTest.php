<?php
/**
 * tests/Helper/SetVersioningTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */

namespace AustinHeap\Database\Encryption\Tests\Helper;

use AustinHeap\Database\Encryption\Tests\TestCase;
use AustinHeap\Database\Encryption\EncryptionHelper;

/**
 * SetVersioningTest
 */
class SetVersioningTest extends TestCase
{
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
}
