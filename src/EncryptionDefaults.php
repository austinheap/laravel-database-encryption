<?php
/**
 * src/EncryptionDefaults.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption;

/**
 * EncryptionDefaults.
 *
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @link        https://packagist.org/packages/austinheap/laravel-database-encryption
 * @link        https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.EncryptionDefaults.html
 */
abstract class EncryptionDefaults
{
    /**
     * Shared default enabled flag.
     *
     * @var bool
     */
    public const DEFAULT_ENABLED = false;

    /**
     * Shared default versioning flag.
     *
     * @var bool
     */
    public const DEFAULT_VERSIONING = true;

    /**
     * Shared default prefix.
     *
     * @var string
     */
    public const DEFAULT_PREFIX = '__LARAVEL-DATABASE-ENCRYPTED-%VERSION%__';

    /**
     * Shared default control characters.
     *
     * @var array
     */
    public const DEFAULT_CONTROL_CHARACTERS = [
        'header' => [
            'start' => 1,
            'stop'  => 4,
        ],
        'prefix' => [
            'start' => 2,
            'stop'  => 3,
        ],
        'type'   => [
            'start' => 30,
            'stop'  => 23,
        ],
    ];

    /**
     * Shared default helpers.
     *
     * @var array
     */
    public const DEFAULT_HELPERS = [
        'database_encryption',
        'db_encryption',
        'dbencryption',
        'database_encrypt',
        'db_encrypt',
        'dbencrypt',
        'database_decrypt',
        'db_decrypt',
        'dbdecrypt',
    ];

    /**
     * Shared default control characters cache.
     *
     * @var null|array
     */
    protected $defaultControlCharactersCache = null;

    /**
     * Shared default prefix cache.
     *
     * @var null|string
     */
    protected $defaultPrefixCache = null;
}
