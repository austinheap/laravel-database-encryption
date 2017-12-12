<?php
/**
 * src/EncryptionServiceProvider.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */
declare(strict_types = 1);

namespace AustinHeap\Database\Encryption;

use Exception;
use Illuminate\Support\Facades\Config;

/**
 * EncryptionServiceProvider.
 *
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @link        https://packagist.org/packages/austinheap/laravel-database-encryption
 * @link        https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.EncryptionServiceProvider.html
 */
class EncryptionServiceProvider extends \Illuminate\Support\ServiceProvider
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
     * Internal default control characters cache.
     *
     * @var null|array
     */
    private static $defaultControlCharactersCache = null;

    /**
     * Internal control characters cache.
     *
     * @var null|array
     */
    private static $controlCharactersCache = null;

    /**
     * Internal prefix cache.
     *
     * @var null|array
     */
    private static $prefixCache = null;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * This method is called after all other service providers have
     * been registered, meaning you have access to all other services
     * that have been registered by the framework.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
                             __DIR__ . '/../config/encryption.php' => config_path('encryption.php'),
                         ]);

        if (!defined('LARAVEL_DATABASE_ENCRYPTION_VERSION')) {
            define('LARAVEL_DATABASE_ENCRYPTION_VERSION', EncryptionServiceProvider::VERSION);
        }

        if (!function_exists('dbencrypt') || !function_exists('dbdecrypt')) {
            throw new Exception('laravel-security-txt v' . self::getVersion() . ' helpers never loaded.');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->commands([
                            \AustinHeap\Database\Encryption\Console\Commands\MigrateEncryptionCommand::class,
                        ]);
    }

    /**
     * Get the package version.
     *
     * @return string
     */
    public static function getVersion(): string
    {
        if (!defined('LARAVEL_DATABASE_ENCRYPTION_VERSION')) {
            throw new Exception('laravel-database-encryption v' . self::getVersion() . ' did not boot.');
        }

        return LARAVEL_DATABASE_ENCRYPTION_VERSION;
    }

    /**
     * Get the package version in parts.
     *
     * @return array
     */
    public static function getVersionParts($padding = null): array
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

    /**
     * Get the package version for a prefix.
     *
     * @return string
     */
    public static function getVersionForPrefix(): string
    {
        $parts = self::getVersionParts(2);

        return 'V-' . implode('-', array_map(function ($part) {
                $part = (string)$part;

                return strlen($part) == 2 ? $part : '0' . $part;
            }, $parts));
    }

    /**
     * Get the encryption prefix setting from configuration.
     *
     * @return string
     */
    public static function getEncryptionPrefix(): string
    {
        if (is_null(self::$prefixCache)) {
            $prefix = Config::get('encryption.prefix', null);
            $prefix = !empty($prefix) && is_string($prefix) ? $prefix : '__ENCRYPTED-%VERSION%__:';

            self::$prefixCache = self::getEncryptionVersioning() ?
                str_replace('%VERSION%', self::getVersionForPrefix(), $prefix) :
                $prefix;
        }

        return self::$prefixCache;
    }

    /**
     * Get the encryption control characters.
     *
     * @return array
     */
    public static function getControlCharacters(?string $type = null): array
    {
        $controls = self::getDefaultControlCharacters();

        if (!is_null($type)) {
            if (array_key_exists($type, $controls)) {
                return $controls[$type];
            } else {
                throw new Exception('Control characters do not exist for $type: "' . (empty($type) ? '(empty)' : $type) . '".');
            }
        }

        return $controls;
    }

    /**
     * Get the default control characters.
     *
     * @return array
     */
    public static function getDefaultControlCharacters(): array
    {
        if (is_null(self::$defaultControlCharactersCache)) {
            $controls = [];

            foreach (self::DEFAULT_CONTROL_CHARACTERS as $control => $config) {
                $controls[$control] = [];

                foreach (['start', 'stop'] as $mode) {
                    $controls[$control][$mode] = [
                        'int'     => $config[$mode],
                        'string'  => chr($config[$mode]),
                        'default' => true,
                    ];
                };
            }

            self::$defaultControlCharactersCache = $controls;
        }

        return self::$defaultControlCharactersCache;
    }

    /**
     * Get the encryption versioning setting from configuration.
     *
     * @return bool
     */
    public static function getEncryptionVersioning(): bool
    {
        $versioning = Config::get('encryption.versioning', null);

        return !is_null($versioning) && is_bool($versioning) ? $versioning : true;
    }
}
