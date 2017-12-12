<?php
/**
 * src/helpers.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.1.0
 */
declare(strict_types=1);

if (! function_exists('dbencrypt')) {
    /**
     * @return null|string
     */
    function dbencrypt(string $value): ?string
    {
        return __FUNCTION__ . ': FUNCTION-NOT-IMPLEMENTED';
    }
}

if (! function_exists('dbdecrypt')) {
    /**
     * @return null|string
     */
    function dbdecrypt(string $value): ?string
    {
        return __FUNCTION__ . ': FUNCTION-NOT-IMPLEMENTED';
    }
}
