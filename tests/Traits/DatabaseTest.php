<?php
/**
 * tests/Traits/DatabaseTest.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.2.0
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
        $model = DatabaseModel::create($this->randomValues());

        $this->assertTrue($model->exists);
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

    public function testUpdateShouldBeEncrypted()
    {
        $strings = $this->randomValues();
        $model = DatabaseModel::create($strings);

        $this->assertTrue($model->exists);
        $this->assertTrue(self::callProtectedMethod($model, 'shouldEncrypt', ['should_be_encrypted']));

        $this->assertNotEquals($strings['should_be_encrypted'], $model->getOriginal('should_be_encrypted'));
        $this->assertEquals($strings['should_be_encrypted'], $model->should_be_encrypted);

        $strings   = $this->randomValues();
        $new_model = DatabaseModel::findOrFail($model->id);

        $this->assertTrue(self::callProtectedMethod($model, 'shouldEncrypt', ['should_be_encrypted']));

        $new_model->update(['should_be_encrypted' => $strings['should_be_encrypted']]);

        $this->assertNotEquals($model->getOriginal('should_be_encrypted'), $new_model->getOriginal('should_be_encrypted'));
        $this->assertNotEquals($model->should_be_encrypted, $new_model->should_be_encrypted);
        $this->assertEquals($model->getOriginal('shouldnt_be_encrypted'), $new_model->getOriginal('shouldnt_be_encrypted'));
        $this->assertEquals($model->shouldnt_be_encrypted, $new_model->shouldnt_be_encrypted);
    }

    public function testUpdateShouldntBeEncrypted()
    {
        $strings = $this->randomValues();
        $model   = DatabaseModel::create($strings);

        $this->assertTrue($model->exists);
        $this->assertFalse(self::callProtectedMethod($model, 'shouldEncrypt', ['shouldnt_be_encrypted']));

        $this->assertEquals($strings['shouldnt_be_encrypted'], $model->getOriginal('shouldnt_be_encrypted'));
        $this->assertEquals($strings['shouldnt_be_encrypted'], $model->shouldnt_be_encrypted);

        $strings   = $this->randomValues();
        $new_model = DatabaseModel::findOrFail($model->id);

        $this->assertFalse(self::callProtectedMethod($model, 'shouldEncrypt', ['shouldnt_be_encrypted']));

        $new_model->update(['shouldnt_be_encrypted' => $strings['shouldnt_be_encrypted']]);

        $this->assertEquals($model->getOriginal('should_be_encrypted'), $new_model->getOriginal('should_be_encrypted'));
        $this->assertEquals($model->should_be_encrypted, $new_model->should_be_encrypted);
        $this->assertNotEquals($model->getOriginal('shouldnt_be_encrypted'), $new_model->getOriginal('shouldnt_be_encrypted'));
        $this->assertNotEquals($model->shouldnt_be_encrypted, $new_model->shouldnt_be_encrypted);
    }

    public function testGetArrayableAttributes()
    {
        $strings = $this->randomValues();
        $model   = DatabaseModel::create($strings);

        $this->assertTrue($model->exists);

        $attributes = $this->callProtectedMethod($model, 'getArrayableAttributes');

        $this->assertTrue(is_array($attributes));
        $this->assertCount(9, $attributes);

        foreach (['should_be_encrypted', 'shouldnt_be_encrypted',
                  'should_be_encrypted_float', 'shouldnt_be_encrypted_float',
                  'should_be_encrypted_int', 'shouldnt_be_encrypted_int',
                  'updated_at', 'created_at', 'id'] as $key) {
            $this->assertArrayHasKey($key, $attributes);
        }

        $this->assertEquals($strings['shouldnt_be_encrypted'], $attributes['shouldnt_be_encrypted']);
        $this->assertEquals($strings['should_be_encrypted'], $attributes['should_be_encrypted']);
    }
}
