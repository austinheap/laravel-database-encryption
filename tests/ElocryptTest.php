<?php
/**
 * Class ElocryptTest.
 *
 * @author del
 */

/**
 * Class ElocryptTest.
 *
 * Test case for ElocryptFive
 */
class ElocryptTest extends PHPUnit_Framework_TestCase
{
    protected function doTest($attributes)
    {
        $model = new DummyModel($attributes);
        $model_attributes = $model->getAttributes();

        $this->assertEquals($attributes['dont_encrypt'], $model->attributes['dont_encrypt']);
        $this->assertStringStartsWith('__ELOCRYPT__:', $model->attributes['encrypt_me']);
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
            'dont_encrypt'  => '12345+12345@gmail.com',
            'encrypt_me'    => 'abcde+12345@gmail.com',
        ]);
    }
}
