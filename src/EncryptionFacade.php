<?php
/**
 * src/EncryptionFacade.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */
declare(strict_types=1);

namespace AustinHeap\Database\Encryption;

use RuntimeException;

/**
 * EncryptionFacade.
 *
 * @link        https://github.com/austinheap/laravel-database-encryption
 * @link        https://packagist.org/packages/austinheap/laravel-database-encryption
 * @link        https://austinheap.github.io/laravel-database-encryption/classes/AustinHeap.Database.Encryption.EncryptionFacade.html
 */
class EncryptionFacade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'DatabaseEncryption';
    }

    /**
     * Get the singleton of EncryptionHelper.
     *
     * @return EncryptionHelper
     */
    public static function getInstance()
    {
        return app(self::getFacadeAccessor());
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        throw_if(! $instance, RuntimeException::class, 'A facade root has not been set.');
        throw_if(! method_exists($instance, $method), RuntimeException::class, 'Method "'.$method.'" does not exist on "'.get_class($instance).'".');

        return $instance->$method(...$args);
    }
}
