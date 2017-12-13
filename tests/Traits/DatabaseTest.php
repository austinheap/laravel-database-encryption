<?php
/**
 * tests/Traits/DatabaseTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.1.0
 */

namespace AustinHeap\Database\Encryption\Tests\Traits;

use AustinHeap\Database\Encryption\Tests\DatabaseTestCase;
use AustinHeap\Database\Encryption\Tests\Models\DatabaseModel;

/**
 * DatabaseTest
 */
class DatabaseTest extends DatabaseTestCase
{
    public function testCreate()
    {
        $model = DatabaseModel::create($this->randomStrings());

        $this->assertTrue($model->exists);
    }

    public function testUpdate()
    {
        $model = DatabaseModel::create($this->randomStrings());

        $this->assertTrue($model->exists);

        $new_model = DatabaseModel::findOrFail($model->id);
        $new_model->update($this->randomStrings());

        $this->assertNotEquals($model->getOriginal('should_be_encrypted'), $new_model->getOriginal('should_be_encrypted'));
        $this->assertNotEquals($model->should_be_encrypted, $new_model->should_be_encrypted);
        $this->assertNotEquals($model->getOriginal('shouldnt_be_encrypted'), $new_model->getOriginal('shouldnt_be_encrypted'));
        $this->assertNotEquals($model->shouldnt_be_encrypted, $new_model->shouldnt_be_encrypted);
    }

    public function testUpdateShouldBeEncrypted()
    {
        $model = DatabaseModel::create($this->randomStrings());

        $this->assertTrue($model->exists);

        $strings   = $this->randomStrings();
        $new_model = DatabaseModel::findOrFail($model->id);
        $new_model->update(['should_be_encrypted' => $strings['should_be_encrypted']]);

        $this->assertNotEquals($model->getOriginal('should_be_encrypted'), $new_model->getOriginal('should_be_encrypted'));
        $this->assertNotEquals($model->should_be_encrypted, $new_model->should_be_encrypted);
        $this->assertEquals($model->getOriginal('shouldnt_be_encrypted'), $new_model->getOriginal('shouldnt_be_encrypted'));
        $this->assertEquals($model->shouldnt_be_encrypted, $new_model->shouldnt_be_encrypted);
    }

    public function testUpdateShouldntBeEncrypted()
    {
        $model = DatabaseModel::create($this->randomStrings());

        $this->assertTrue($model->exists);

        $strings   = $this->randomStrings();
        $new_model = DatabaseModel::findOrFail($model->id);
        $new_model->update(['shouldnt_be_encrypted' => $strings['shouldnt_be_encrypted']]);

        $this->assertEquals($model->getOriginal('should_be_encrypted'), $new_model->getOriginal('should_be_encrypted'));
        $this->assertEquals($model->should_be_encrypted, $new_model->should_be_encrypted);
        $this->assertNotEquals($model->getOriginal('shouldnt_be_encrypted'), $new_model->getOriginal('shouldnt_be_encrypted'));
        $this->assertNotEquals($model->shouldnt_be_encrypted, $new_model->shouldnt_be_encrypted);
    }
}
