<?php
/**
 * tests/Traits/FloatTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Traits;

use AustinHeap\Database\Encryption\Tests\DatabaseTestCase;
use AustinHeap\Database\Encryption\Tests\Models\DatabaseModel;

/**
 * FloatTest
 */
class FloatTest extends DatabaseTestCase
{
    public function testCreate()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertFalse(empty($model->should_be_encrypted_float));
    }

    public function testShouldBeEncryptedFloat()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertTrue($model->exists);
        $this->assertNotFalse(strpos($model->getOriginal('should_be_encrypted_float'), '__LARAVEL-DATABASE-ENCRYPTED-'));
        $this->assertFalse(is_int($model->should_be_encrypted_float));
        $this->assertTrue(is_float($model->should_be_encrypted_float));
        $this->assertTrue(is_double($model->should_be_encrypted_float));
        $this->assertFalse(is_string($model->should_be_encrypted_float));
    }

    public function testShouldntBeEncryptedFloat()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertTrue($model->exists);
        $this->assertFalse(is_int($model->shouldnt_be_encrypted_float));
        $this->assertTrue(is_float($model->shouldnt_be_encrypted_float));
        $this->assertTrue(is_double($model->shouldnt_be_encrypted_float));
        $this->assertFalse(is_string($model->should_be_encrypted_float));
    }
}
