<?php
/**
 * src/config/database-encryption.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.0.1
 */

return [

    /**
     * Enable database encryption.
     *
     * Default: false
     *
     * @var null|bool
     */
    'enable'             => env('DATABASE_ENCRYPTION_ENABLE', false),

    /**
     * Prefix used in attribute header.
     *
     * Default: __ENCRYPTED-%VERSION%__:
     *
     * @var null|string
     */
    'prefix'             => env('DATABASE_ENCRYPTION_PREFIX', '__ENCRYPTED-%VERSION%__:'),

    /**
     * Enable header versioning.
     *
     * Default: true
     *
     * @var null|bool
     */
    'versioning'         => env('DATABASE_ENCRYPTION_VERSIONING', true),

    /**
     * Control characters used in header payload.
     *
     * Default: [
     *     'header' => [
     *         'start' => 1, // or: chr(1)
     *         'stop'  => 4, // or: chr(4)
     *     ],
     *     'prefix' => [
     *         'start' => 2, // or: chr(2)
     *         'stop'  => 3, // or: chr(3)
     *     ],
     *     'type'   => [
     *         'start' => 30, // or: chr(30)
     *         'stop'  => 23, // or: chr(23)
     *     ],
     * ]
     *
     * @var null|array
     */
    'control_characters' => null,

];
