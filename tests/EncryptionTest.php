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
        for ($x = 0; $x < LARAVEL_DATABASE_ENCRYPTION_ITERATIONS; $x++) {
            $model            = new DummyModel($attributes);
            $model_attributes = $model->getAttributes();
            $prefix           = self::callProtectedMethod($model, 'getEncryptionPrefix');

            $this->assertEquals($attributes['dont_encrypt'], $model->attributes['dont_encrypt']);
            $this->assertStringStartsWith($prefix, $model->attributes['encrypt_me']);
            $this->assertEquals($attributes['encrypt_me'], $model_attributes['encrypt_me']);
        }
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
}
