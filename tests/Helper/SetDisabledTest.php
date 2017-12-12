<?php
/**
 * tests/Helper/SetDisabledTest.php
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
 * SetDisabledTest
 */
class SetDisabledTest extends TestCase
{
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
}
