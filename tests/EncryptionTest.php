<?php
/**
 * tests/EncryptionTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */

namespace AustinHeap\Database\Encryption\Tests;

/**
 * EncryptionTest
 */
class EncryptionTest extends TestCase
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
                          'dont_encrypt' => '12345',
                          'encrypt_me'   => 'abcde',
                      ]);
    }

    public function testEncryptStringWithPlus()
    {
        $this->doTest([
                          'dont_encrypt' => '12345+12345@gmail.com',
                          'encrypt_me'   => 'abcde+12345@gmail.com',
                      ]);
    }
}
