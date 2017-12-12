<?php

namespace AustinHeap\Database\Encryption;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class EncryptionServiceProvider extends ServiceProvider
{
    public const VERSION = '0.0.1';

    /**
     * Bootstrap the application services.
     *
     * This method is called after all other service providers have
     * been registered, meaning you have access to all other services
     * that have been registered by the framework.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
                             __DIR__ . '/config/encryption.php' => config_path('encryption.php'),
                         ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the package version.
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Get the package version as a prefix.
     *
     * @return string
     */
    public static function getVersionPrefix(): string
    {
        return strtoupper(str_replace('.', '', self::getVersion()));
    }

    /**
     * Get the encrypted value prefix.
     *
     * @return string
     */
    public static function getEncryptionPrefix(): string
    {
        $prefix     = Config::get('encryption.prefix', null);
        $prefix     = !empty($prefix) && is_string($prefix) ? $prefix : '__ENCRYPTED-%VERSION%__:';

        $versioning = Config::get('encryption.versioning', false);
        $versioning = is_bool($versioning) ? $versioning : true;

        return $versioning ? str_replace('%VERSION%', self::getVersionPrefix(), $prefix) : $prefix;
    }
}
