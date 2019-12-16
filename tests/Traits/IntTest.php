<?php
/**
 * tests/Traits/IntTest.php
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
 * IntTest
 */
class IntTest extends DatabaseTestCase
{
    public function testCreate()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertFalse(empty($model->should_be_encrypted_int));
    }

    public function testShouldBeEncryptedInt()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertTrue($model->exists);
        $this->assertNotFalse(strpos($model->getOriginal('should_be_encrypted_int'), '__LARAVEL-DATABASE-ENCRYPTED-'));
        $this->assertTrue(is_int($model->should_be_encrypted_int));
        $this->assertFalse(is_float($model->should_be_encrypted_int));
        $this->assertFalse(is_double($model->should_be_encrypted_int));
        $this->assertFalse(is_string($model->should_be_encrypted_int));
    }

    public function testShouldntBeEncryptedInt()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertTrue($model->exists);
        $this->assertTrue(is_int($model->shouldnt_be_encrypted_int));
        $this->assertFalse(is_float($model->shouldnt_be_encrypted_int));
        $this->assertFalse(is_double($model->shouldnt_be_encrypted_int));
        $this->assertFalse(is_string($model->should_be_encrypted_int));
    }
}
