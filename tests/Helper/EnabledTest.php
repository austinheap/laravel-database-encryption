<?php
/**
 * tests/Helper/SetEnabledTestt.php
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
 * EnabledTest
 */
class EnabledTest extends TestCase
{
    public function testEnabledCached()
    {
        $helper = (new EncryptionHelper())->setEnabled(null);

        $this->assertAttributeEquals(null, 'enabledCache', $helper);
        $this->assertTrue(is_bool($helper->isEnabled()));
        $this->assertAttributeNotSame(null, 'enabledCache', $helper);
    }

    public function testEnabledTrue()
    {
        $helper = (new EncryptionHelper())->setEnabled(true);

        $this->assertTrue($helper->isEnabled());
    }

    public function testEnabledFalse()
    {
        $helper = new EncryptionHelper();

        $helper->setEnabled(false);
        $this->assertFalse($helper->isEnabled());
    }
}
