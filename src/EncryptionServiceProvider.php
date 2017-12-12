<?php
/**
 * src/EncryptionServiceProvider.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */
declare(strict_types = 1);

namespace AustinHeap\Database\Encryption;

use Config, DatabaseEncryption, Exception;

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
                             __DIR__ . '/../config/database-encryption.php' => config_path('database-encryption.php'),
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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/database-encryption.php', 'database-encryption'
        );

        $this->app->singleton(EncryptionFacade::getFacadeRoot(), function ($app) {
            return new EncryptionHelper();
        });

        $this->commands([\AustinHeap\Database\Encryption\Console\Commands\MigrateEncryptionCommand::class]);
    }

    /**
     * Get the package version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return DatabaseEncryption::getVersion();
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
}
