<?php
/**
 * src/EncryptionServiceProvider.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */
declare(strict_types = 1);

namespace AustinHeap\Database\Encryption;

use Config;

//use DatabaseEncryption;

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
            define('LARAVEL_DATABASE_ENCRYPTION_VERSION', EncryptionHelper::VERSION);
        }

        foreach (['dbencrypt', 'dbdecrypt'] as $function) {
            throw_if(!function_exists($function), 'The provider did not boot helper function: "' . $function . '".');
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

        $this->app->singleton(EncryptionFacade::getFacadeAccessor(), function ($app) {
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
        return ''; //DatabaseEncryption::getVersion();
    }

    /**
     * Get the package version for a prefix.
     *
     * @return string
     */
    public function getVersionForPrefix(): string
    {
        return ''; //DatabaseEncryption::getVersionForPrefix();
    }

    /**
     * Get the prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return ''; //DatabaseEncryption::getVersionForPrefix();
    }

    /**
     * Get the control characters.
     *
     * @return array
     */
    public function getControlCharacters(?string $type = null): array
    {
        return []; //DatabaseEncryption::getControlCharacters();
    }
}
