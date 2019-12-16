<?php
/**
 * src/EncryptionDefaults.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption;

use RuntimeException;

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
        'field'   => [
            'start'     => 30,
            'delimiter' => 25,
            'stop'      => 23,
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
     * Private default control characters cache.
     *
     * @var null|array
     */
    private static $defaultControlCharactersCache = null;

    /**
     * Private default prefix cache.
     *
     * @var null|string
     */
    private static $defaultPrefixCache = null;

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
        return ! static::isEnabledDefault();
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
        return ! static::isVersioningDefault();
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
    public static function getControlCharactersDefault(?string $type = null, bool $raw = false): array
    {
        if (is_null(self::$defaultControlCharactersCache)) {
            $characters = [];

            foreach (self::DEFAULT_CONTROL_CHARACTERS as $control => $config) {
                $characters[$control] = [];

                foreach (['start', 'delimiter', 'stop'] as $mode) {
                    if (isset($config[$mode])) {
                        $characters[$control][$mode] = self::buildCharacter($config[$mode], true);
                    }
                }
            }

            self::$defaultControlCharactersCache = $characters;
        }

        if (! is_null($type)) {
            throw_if(! array_key_exists($type, self::$defaultControlCharactersCache), RuntimeException::class,
                     'Default control characters do not exist for $type: "'.(empty($type) ? '(empty)' : $type).'".');

            return self::$defaultControlCharactersCache[$type];
        }

        return self::$defaultControlCharactersCache;
    }

    /**
     * @return array
     */
    public static function getHelpersDefault(): array
    {
        return static::DEFAULT_HELPERS;
    }

    /**
     * Builds array of character information from character.
     *
     * @return array
     */
    public static function buildCharacter($character, bool $default = false): array
    {
        throw_if(! is_int($character) && ! is_string($character), RuntimeException::class,
                 'Cannot build character array from $character type: "'.gettype($character).'".');

        return [
            'int'     => is_int($character) ? $character : ord($character),
            'string'  => is_string($character) ? $character : chr($character),
            'default' => $default,
        ];
    }
}
