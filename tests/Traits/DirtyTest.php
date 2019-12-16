<?php
/**
 * tests/Traits/DirtyTest.php
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
 * DirtyTest
 */
class DirtyTest extends DatabaseTestCase
{
    public function testCreate()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertTrue(method_exists($model, 'getDirty'));
        $this->assertCount(0, $model->getDirty());
    }

    public function testUpdate()
    {
        $model = DatabaseModel::create($this->randomValues());

        $this->assertTrue($model->exists);

        $new_model = DatabaseModel::findOrFail($model->id);
        $new_model->update($this->randomValues());

        $this->assertNotEquals($model->getOriginal('should_be_encrypted'), $new_model->getOriginal('should_be_encrypted'));
        $this->assertNotEquals($model->should_be_encrypted, $new_model->should_be_encrypted);
        $this->assertNotEquals($model->getOriginal('shouldnt_be_encrypted'), $new_model->getOriginal('shouldnt_be_encrypted'));
        $this->assertNotEquals($model->shouldnt_be_encrypted, $new_model->shouldnt_be_encrypted);
    }
}
