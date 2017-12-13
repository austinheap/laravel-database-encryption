<?php
/**
 * tests/Models/DatabaseModel.php
 *
 * @package     laravel-database-encryption
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.1.0
 */

namespace AustinHeap\Database\Encryption\Tests\Models;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;

/**
 * DatabaseModel
 */
class DatabaseModel extends RealModel
{
    use HasEncryptedAttributes;

    public    $table     = 'test_models';
    protected $fillable  = ['shouldnt_be_encrypted', 'should_be_encrypted'];
    protected $encrypted = ['should_be_encrypted'];
}
