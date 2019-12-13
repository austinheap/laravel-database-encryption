<?php
/**
 * src/EncryptionHelper.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption;

use Illuminate\Support\Facades\Config;
use RuntimeException;

/**
 * EncryptionHelper.
 *
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @link        https://packagist.org/packages/austinheap/laravel-database-encryption
 * @link        https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.EncryptionHelper.html
 */
class EncryptionHelper extends EncryptionDefaults
{
    /**
     * Internal version number.
     *
     * @var string
     */
    public const VERSION = '0.1.2';

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
     * Private control characters cache.
     *
     * @var null|array
     */
    private $controlCharactersCache = null;

    /**
     * Private prefix cache.
     *
     * @var null|string
     */
    private $prefixCache = null;

    /**
     * Private header prefix cache.
     *
     * @var null|string
     */
    private $headerPrefixCache = null;

    /**
     * EncryptionHelper constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Reset the class; mostly the cache.
     *
     * @return EncryptionHelper
     */
    public function reset(): self
    {
        $this->enabledCache =
        $this->versioningCache =
        $this->versionPartsCache =
        $this->controlCharactersCache =
        $this->prefixCache =
        $this->headerPrefixCache = null;

        return $this;
    }

    /**
     * Get the package version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        throw_if(! defined('LARAVEL_DATABASE_ENCRYPTION_VERSION'), RuntimeException::class,
                 'The provider did not boot.');

        return LARAVEL_DATABASE_ENCRYPTION_VERSION;
    }

    /**
     * Set the enabled flag.
     *
     * @return bool
     */
    public function setEnabled(?bool $value = null): self
    {
        $this->enabledCache = $value;

        return $this;
    }

    /**
     * Check the enabled flag.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        if (is_null($this->enabledCache)) {
            $enabled = Config('database-encryption.enabled', null);
            $this->enabledCache = ! is_null($enabled) && is_bool($enabled) ? $enabled : self::isEnabledDefault();
        }

        return $this->enabledCache;
    }

    /**
     * Set the enabled flag inverse.
     *
     * @return bool
     */
    public function setDisabled(?bool $value = null): self
    {
        return $this->setEnabled(is_bool($value) ? ! $value : null);
    }

    /**
     * Check the enabled flag inverse.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return ! $this->isEnabled();
    }

    /**
     * Set the versioning flag.
     *
     * @return null|bool
     */
    public function setVersioning(?bool $value = null): self
    {
        $this->reset();
        $this->versioningCache = $value;

        return $this;
    }

    /**
     * Check the versioning flag.
     *
     * @return bool
     */
    public function isVersioning(): bool
    {
        if (is_null($this->versioningCache)) {
            $versioning = Config('database-encryption.versioning', null);
            $this->versioningCache = ! is_null($versioning) && is_bool($versioning) ? $versioning : self::isVersioningDefault();
        }

        return $this->versioningCache;
    }

    /**
     * Set the enabled flag inverse.
     *
     * @return null|bool
     */
    public function setVersionless(?bool $value = null): self
    {
        return $this->setVersioning(is_bool($value) ? ! $value : null);
    }

    /**
     * Check the versioning flag inverse.
     *
     * @return bool
     */
    public function isVersionless(): bool
    {
        return ! $this->isVersioning();
    }

    /**
     * Get the package version in parts.
     *
     * @return array
     */
    public function getVersionParts(?int $padding = null): array
    {
        if (! is_array($this->versionPartsCache)) {
            $this->versionPartsCache = [];
        }

        $padding = is_int($padding) && $padding === 0 ? null : $padding;
        $key = 'padding-'.(is_null($padding) ? 'null' : (string) $padding);

        if (! array_key_exists($key, $this->versionPartsCache)) {
            $parts = explode('.', $this->getVersion());

            $this->versionPartsCache[$key] = array_map(function ($part) use ($padding) {
                $part = (string) $part;

                if (is_null($padding)) {
                    return $part;
                }

                $length = strlen(is_string($part) ? $part : (string) $part);

                return $length == $padding ? $part : str_repeat('0', $padding - $length).$part;
            }, $parts);
        }

        return $this->versionPartsCache[$key];
    }

    /**
     * Get the package version for a prefix.
     *
     * @return string
     */
    public function getVersionForPrefix(int $padding = 2, string $glue = '-'): string
    {
        return 'VERSION-'.implode($glue, $this->getVersionParts($padding));
    }

    /**
     * Set the configured header prefix.
     *
     * @return EncryptionHelper
     */
    public function setHeaderPrefix(?string $value = null): self
    {
        throw_if(is_string($value) && strlen(trim($value)) == 0, RuntimeException::class,
                 'Cannot use empty string as header prefix.');

        $this->headerPrefixCache = $value;

        return $this;
    }

    /**
     * Get the configured header prefix.
     *
     * @return string
     */
    public function getHeaderPrefix(): string
    {
        if (is_null($this->headerPrefixCache)) {
            $characters = $this->getControlCharacters();

            $this->headerPrefixCache = $characters['header']['start']['string'].
                                       $characters['prefix']['start']['string'].
                                       $this->getPrefix().
                                       $characters['prefix']['stop']['string'];
        }

        return $this->headerPrefixCache;
    }

    /**
     * Build a header string, optionally with an object.
     *
     * @return string
     */
    public function buildHeader($object = null): string
    {
        $characters = $this->getControlCharacters();

        return $characters['header']['start']['string'].
               $characters['prefix']['start']['string'].
               $this->getPrefix().
               $characters['prefix']['stop']['string'].
               $characters['field']['start']['string'].
               'version'.
               $characters['field']['delimiter']['string'].
               self::getVersionForPrefix().
               $characters['field']['stop']['string'].
               (is_null($object) ? '' :
                   $characters['field']['start']['string'].
                   'type'.
                   $characters['field']['delimiter']['string'].
                   gettype($object).'['.(is_object($object) ? get_class($object) : 'native').']'.
                   $characters['field']['stop']['string']
               ).
               $characters['header']['stop']['string'];
    }

    /**
     * Set the configured prefix.
     *
     * @return EncryptionHelper
     */
    public function setPrefix(?string $value = null): self
    {
        throw_if(is_string($value) && strlen(trim($value)) == 0, RuntimeException::class,
                 'Cannot use empty string as prefix.');

        $this->prefixCache = is_string($value) && $this->isVersioning() ?
            str_replace('%VERSION%', $this->getVersionForPrefix(), $value) :
            $value;

        return $this;
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
            $prefix = ! empty($prefix) && is_string($prefix) ? $prefix : self::getPrefixDefault();

            $this->prefixCache = $this->isVersioning() ?
                str_replace('%VERSION%', $this->getVersionForPrefix(), $prefix) :
                $prefix;
        }

        return $this->prefixCache;
    }

    /**
     * Get the configured control characters.
     *
     * @return array
     */
    public function getControlCharacters(?string $type = null): array
    {
        $defaults = self::getControlCharactersDefault();
        $characters = self::getControlCharactersDefault();

        if (! is_null($type)) {
            throw_if(! array_key_exists($type, $characters),
                     'Control characters do not exist for $type: "'.(empty($type) ? '(empty)' : $type).'".');

            return $characters[$type];
        }

        return $characters;
    }

    /**
     * Get the singleton of this class.
     *
     * @return EncryptionHelper
     */
    public static function getInstance(): self
    {
        return EncryptionFacade::getInstance();
    }
}
