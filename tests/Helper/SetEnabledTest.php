<?php
/**
 * tests/Helper/SetEnabledTestt.php
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
 * SetEnabledTest
 */
class SetEnabledTest extends TestCase
{
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
}
