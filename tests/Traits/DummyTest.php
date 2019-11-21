<?php
/**
 * tests/Traits/DummyTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.2.1
 */

namespace AustinHeap\Database\Encryption\Tests\Traits;

use AustinHeap\Database\Encryption\Tests\TestCase;
use AustinHeap\Database\Encryption\Tests\Models\DummyModel;

/**
 * DummyTest
 */
class DummyTest extends TestCase
{
    protected function doTest($attributes)
    {
        $model            = new DummyModel($attributes);
        $model_attributes = $model->getAttributes();
        $prefix           = self::callProtectedMethod($model, 'getEncryptionPrefix');

        $this->assertEquals($attributes['dont_encrypt'], $model->attributes['dont_encrypt']);
        $this->assertStringStartsWith($prefix, $model->attributes['encrypt_me']);
        $this->assertEquals($attributes['encrypt_me'], $model_attributes['encrypt_me']);
    }

    public function testEncryptNormalString()
    {
        $this->doTest([
                          'dont_encrypt' => 'dont-encrypt-' . str_random(),
                          'encrypt_me'   => 'encrypt-me-' . str_random(),
                      ]);
    }

    public function testEncryptStringWithPlus()
    {
        $this->doTest([
            'dont_encrypt' => '12345+' . rand(111111, 999999) . '@gmail.com',
            'encrypt_me'   => 'abcde+' . str_random() . '@gmail.com',
        ]);
    }

    public function testIsEncryptable()
    {
        $attributes = [
            'dont_encrypt' => '12345+' . rand(111111, 999999) . '@gmail.com',
            'encrypt_me'   => 'abcde+' . str_random() . '@gmail.com',
        ];
        $model      = new DummyModel($attributes);

        $this->assertTrue(self::callProtectedMethod($model, 'isEncryptable'));
    }
}
