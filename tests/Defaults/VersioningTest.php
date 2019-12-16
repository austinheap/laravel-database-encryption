<?php
/**
 * tests/Defaults/VersioningTest.php
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
 * VersioningTest
 */
class VersioningTest extends TestCase
{
    public function testVersioning()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertEquals(true, $defaults::DEFAULT_VERSIONING);
        $this->assertSame($defaults::DEFAULT_VERSIONING, $defaults->isVersioningDefault());
        $this->assertSame(!$defaults::DEFAULT_VERSIONING, $defaults->isVersionlessDefault());
    }
}
