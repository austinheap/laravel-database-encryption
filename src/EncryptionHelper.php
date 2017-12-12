<?php
/**
 * src/EncryptionHelper.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */
declare(strict_types = 1);

namespace AustinHeap\Database\Encryption;

/**
 * EncryptionHelper.
 *
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @link        https://packagist.org/packages/austinheap/laravel-database-encryption
 * @link        https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.EncryptionHelper.html
 */
class EncryptionHelper
{
    /**
     * Internal version number.
     *
     * @var string
     */
    public const VERSION = '0.0.1';

    /**
     * Internal default control characters.
     *
     * @var array
     */
    private const DEFAULT_CONTROL_CHARACTERS = [
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
     * Private default control characters cache.
     *
     * @var null|array
     */
    private $defaultControlCharactersCache = null;

    /**
     * Private control characters cache.
     *
     * @var null|array
     */
    private $controlCharactersCache = null;

    /**
     * Private prefix cache.
     *
     * @var null|array
     */
    private $prefixCache = null;

    /**
     * Get the package version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        throw_if(!defined('LARAVEL_DATABASE_ENCRYPTION_VERSION'), 'The provider did not boot.');

        return LARAVEL_DATABASE_ENCRYPTION_VERSION;
    }

    /**
     * Get the package version in parts.
     *
     * @return array
     */
    public function getVersionParts($padding = null): array
    {
        $parts = explode('.', self::getVersion());

        return array_map(function ($part) use ($padding) {
            $part = (string)$part;
            if (is_null($padding)) {
                return $part;
            } else {
                $length = strlen($part);

                return $length == $padding ? $part : str_repeat('0', $padding - $length) . $part;
            }
        }, $parts);
    }
}
