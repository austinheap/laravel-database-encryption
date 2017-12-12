<?php
/**
 * src/EncryptionHelper.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */
declare(strict_types = 1);

namespace AustinHeap\Database\Encryption;

use Config;

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
     * Private enable flag cache.
     *
     * @var null|bool
     */
    private $enabledCache = null;

    /**
     * Private versioning flag cache.
     *
     * @var null|bool
     */
    private $versioningCache = null;

    /**
     * Private version parts cache.
     *
     * @var null|array
     */
    private $versionPartsCache = null;

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
     * Private default prefix cache.
     *
     * @var null|string
     */
    private $defaultPrefixCache = null;

    /**
     * Private prefix cache.
     *
     * @var null|string
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
     * Check the enable flag.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        if (is_null($this->enabledCache)) {
            $enabled            = Config('database-encryption.enabled', null);
            $this->enabledCache = !is_null($enabled) && is_bool($enabled) ? $enabled : false;
        }

        return $this->enabledCache;
    }

    /**
     * Check the enable flag inverse.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return !$this->isEnabled();
    }

    /**
     * Check the versioning flag.
     *
     * @return bool
     */
    public function isVersioning(): bool
    {
        if (is_null($this->versioningCache)) {
            $versioning            = Config('database-encryption.versioning', null);
            $this->versioningCache = !is_null($versioning) && is_bool($versioning) ? $versioning : true;
        }

        return $this->versioningCache;
    }

    /**
     * Check the versioning flag inverse.
     *
     * @return bool
     */
    public function isVersionless(): bool
    {
        return !$this->isVersioning();
    }

    /**
     * Get the package version in parts.
     *
     * @return array
     */
    public function getVersionParts(?int $padding = null): array
    {
        if (!is_array($this->versionPartsCache)) {
            $this->versionPartsCache = [];
        }

        $key = 'padding-' . (is_null($padding) ? 'null' : (string)$padding);

        if (!array_key_exists($key, $this->versionPartsCache)) {
            $parts = explode('.', $this->getVersion());

            $this->versionPartsCache[$key] = array_map(function ($part) use ($padding) {
                $part = (string)$part;

                if (is_null($padding)) {
                    return $part;
                }

                $length = strlen($part);

                return $length == $padding ? $part : str_repeat('0', $padding - $length) . $part;
            }, $parts);
        }

        return $this->versionPartsCache[$key];
    }

    /**
     * Get the package version for a prefix.
     *
     * @return string
     */
    public function getVersionForPrefix(): string
    {
        return 'V-' . implode('-', $this->getVersionParts(2));
    }

    /**
     * Get the configured prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        if (is_null($this->prefixCache)) {
            $prefix = Config::get('database-encryption.prefix', null);
            $prefix = !empty($prefix) && is_string($prefix) ? $prefix : '__ENCRYPTED-%VERSION%__:';

            $this->prefixCache = $this->getVersioning() ?
                str_replace('%VERSION%', $this->getVersionForPrefix(), $prefix) :
                $prefix;
        }

        return $this->prefixCache;
    }

    /**
     * Get the default prefix.
     *
     * @return string
     */
    public function getDefaultPrefix(): string
    {
        if (is_null($this->defaultPrefixCache)) {
            $this->defaultPrefixCache = '__ENCRYPTED-%VERSION%__:';
        }

        return $this->defaultPrefixCache;
    }

    /**
     * Get the configured control characters.
     *
     * @return array
     */
    public function getControlCharacters(?string $type = null): array
    {
        $characters = $this->getDefaultControlCharacters();

        if (!is_null($type)) {
            throw_if(!array_key_exists($type, $characters), 'Control characters do not exist for $type: "' . (empty($type) ? '(empty)' : $type) . '".');

            return $characters[$type];
        }

        return $characters;
    }

    /**
     * Get the default control characters.
     *
     * @return array
     */
    public function getDefaultControlCharacters(?string $type = null): array
    {
        if (is_null($this->defaultControlCharactersCache)) {
            $characters = [];

            foreach (self::DEFAULT_CONTROL_CHARACTERS as $control => $config) {
                $characters[$control] = [];

                foreach (['start', 'stop'] as $mode) {
                    $characters[$control][$mode] = $this->buildCharacterArray($config[$mode]);
                };
            }

            $this->defaultControlCharactersCache = $characters;
        }

        if (!is_null($type)) {
            throw_if(!array_key_exists($type, $this->defaultControlCharactersCache), 'Default control characters do not exist for $type: "' . (empty($type) ? '(empty)' : $type) . '".');

            return $this->defaultControlCharactersCache[$type];
        }

        return $this->defaultControlCharactersCache;
    }

    /**
     * Builds array of character information from character.
     *
     * @return array
     */
    private function buildCharacterArray($character, bool $default = false): array
    {
        throw_if(!is_int($character) && !is_string($character), 'Cannot build character array from $character type: "' . gettype($character) . '".');

        return [
            'int'     => is_int($character) ? $character : ord($character),
            'string'  => is_string($character) ? $character : chr($character),
            'default' => $default,
        ];
    }
}
