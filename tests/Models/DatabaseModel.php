<?php
/**
 * tests/Models/DatabaseModel.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

namespace AustinHeap\Database\Encryption\Tests\Models;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

/**
 * DatabaseModel
 */
class DatabaseModel extends RealModel
{
    use HasEncryptedAttributes;

    public $table = 'test_models';

    protected $fillable = [
        'should_be_encrypted',
        'shouldnt_be_encrypted',
        'should_be_encrypted_float',
        'shouldnt_be_encrypted_float',
        'should_be_encrypted_int',
        'shouldnt_be_encrypted_int',
    ];

    protected $encrypted = [
        'should_be_encrypted',
        'should_be_encrypted_float',
        'should_be_encrypted_int',
    ];
}
