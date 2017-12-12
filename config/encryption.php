<?php
/**
 * src/config/encryption.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */

return [

    'prefix'     => env('ENCRYPTION_PREFIX', '__ENCRYPTED-%VERSION%__:'),
    'versioning' => env('ENCRYPTION_VERSIONING', true),
    'control'    => [
        'header' => [
            'start' => 1, // or: chr(1)
            'stop'  => 4, // or: chr(4)
        ],
        'prefix' => [
            'start' => 2, // or: chr(2)
            'stop'  => 3, // or: chr(3)
        ],
        'type'   => [
            'start' => 30, // or: chr(30)
            'stop'  => 23, // or: chr(23)
        ],
    ],

];
