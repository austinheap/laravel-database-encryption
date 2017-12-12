<?php
/**
 * src/EncryptionDefaults.php.
 *
 * @package     AustinHeap\Database\Encryption
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

    /**
     * @return bool
     */
    public static function isEnabledDefault(): bool
    {
        return static::DEFAULT_ENABLED;
    }

    /**
     * @return bool
     */
    public static function isDisabledDefault(): bool
    {
        return !static::isEnabledDefault();
    }

    /**
     * @return bool
     */
    public static function isVersioningDefault(): bool
    {
        return static::DEFAULT_VERSIONING;
    }

    /**
     * @return bool
     */
    public static function isVersionlessDefault(): bool
    {
        return !static::isVersioningDefault();
    }

    /**
     * @return string
     */
    public static function getPrefixDefault(): string
    {
        return static::DEFAULT_PREFIX;
    }

    /**
     * @return array
     */
    public static function getControlCharactersDefault(): array
    {
        return static::DEFAULT_CONTROL_CHARACTERS;
    }

    /**
     * @return array
     */
    public static function getHelpersDefault(): array
    {
        return static::DEFAULT_HELPERS;
    }
}
