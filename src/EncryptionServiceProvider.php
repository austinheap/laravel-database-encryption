<?php
/**
 * src/EncryptionServiceProvider.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption;

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
        $this->publishes([__DIR__.'/../config/database-encryption.php' => config_path('database-encryption.php')]);

        if (! defined('LARAVEL_DATABASE_ENCRYPTION_VERSION')) {
            define('LARAVEL_DATABASE_ENCRYPTION_VERSION', EncryptionHelper::VERSION);
        }

        foreach (EncryptionDefaults::getHelpersDefault() as $helper) {
            throw_if(! empty($helper) && ! function_exists($helper),
                     'The provider did not boot helper function: "'.$helper.'".');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/database-encryption.php', 'database-encryption');

        $this->app->singleton(EncryptionFacade::getFacadeAccessor(), function ($app) {
            return new EncryptionHelper();
        });

        $this->commands([\AustinHeap\Database\Encryption\Console\Commands\MigrateEncryptionCommand::class]);
    }
}
