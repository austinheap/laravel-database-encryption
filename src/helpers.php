<?php
/**
 * src/helpers.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.2.1
 */
declare(strict_types=1);

if (! function_exists('database_encryption')) {
    /**
     * @return \AustinHeap\Database\Encryption\EncryptionHelper
     */
    function database_encryption(): \AustinHeap\Database\Encryption\EncryptionHelper
    {
        return \AustinHeap\Database\Encryption\EncryptionFacade::getInstance();
    }
}

if (! function_exists('db_encryption')) {
    /**
     * @return \AustinHeap\Database\Encryption\EncryptionHelper
     */
    function db_encryption(): \AustinHeap\Database\Encryption\EncryptionHelper
    {
        return database_encryption();
    }
}

if (! function_exists('dbencryption')) {
    /**
     * @return \AustinHeap\Database\Encryption\EncryptionHelper
     */
    function dbencryption(): \AustinHeap\Database\Encryption\EncryptionHelper
    {
        return database_encryption();
    }
}

if (! function_exists('database_encrypt')) {
    /**
     * @return null|string
     */
    function database_encrypt(?string $value = null): ?string
    {
        return __FUNCTION__.': FUNCTION-NOT-IMPLEMENTED';
    }
}

if (! function_exists('db_encrypt')) {
    /**
     * @return null|string
     */
    function db_encrypt(?string $value = null): ?string
    {
        return database_encrypt($value);
    }
}

if (! function_exists('dbencrypt')) {
    /**
     * @return null|string
     */
    function dbencrypt(?string $value = null): ?string
    {
        return database_encrypt($value);
    }
}

if (! function_exists('database_decrypt')) {
    /**
     * @return null|string
     */
    function database_decrypt(string $value): ?string
    {
        return __FUNCTION__.': FUNCTION-NOT-IMPLEMENTED';
    }
}

if (! function_exists('db_decrypt')) {
    /**
     * @return null|string
     */
    function db_decrypt(string $value): ?string
    {
        return database_decrypt($value);
    }
}

if (! function_exists('dbdecrypt')) {
    /**
     * @return null|string
     */
    function dbdecrypt(string $value): ?string
    {
        return database_decrypt($value);
    }
}
