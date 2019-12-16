<?php
/**
 * tests/Defaults/ControlCharactersTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Defaults;

use AustinHeap\Database\Encryption\EncryptionDefaults;
use AustinHeap\Database\Encryption\Tests\TestCase;

/**
 * ControlCharactersTest
 */
class ControlCharactersTest extends TestCase
{
    public function testControlCharacters()
    {
        $defaults   = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $characters = [
            'header' => [
                'start' => 1,
                'stop'  => 4,
            ],
            'prefix' => [
                'start' => 2,
                'stop'  => 3,
            ],
            'field'   => [
                'start'     => 30,
                'delimiter' => 25,
                'stop'      => 23,
            ],
        ];

        $this->assertTrue(method_exists($defaults, 'getControlCharactersDefault'));

        foreach ($characters as $key => $config) {
            $this->assertArrayHasKey($key, $defaults->getControlCharactersDefault());
        }
    }

    public function testControlCharactersTypeValid()
    {
        $defaults = $this->getMockBuilder(EncryptionDefaults::class)->getMockForAbstractClass();
        $this->assertTrue(method_exists($defaults, 'getControlCharactersDefault'));

        $characters = $defaults->getControlCharactersDefault('field');
        $this->assertTrue(is_array($characters));

        foreach (['start', 'delimiter', 'stop'] as $key) {
            $this->assertArrayHasKey($key, $characters);
        }
    }

    //    public function testControlCharactersCache()
    //    {
    // broken
    //    }
}
