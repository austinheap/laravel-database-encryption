<?php
/**
 * src/config/database-encryption.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.3.0
 */

return [

    /*
     * Enable database encryption.
     *
     * Default: false
     *
     * @var null|bool
     */
    'enabled'            => env('DB_ENCRYPTION_ENABLED', false),

    /*
     * Prefix used in attribute header.
     *
     * Default: __LARAVEL-DATABASE-ENCRYPTED-%VERSION%__
     *
     * @var null|string
     */
    'prefix'             => env('DB_ENCRYPTION_PREFIX', '__LARAVEL-DATABASE-ENCRYPTED-%VERSION%__'),

    /*
     * Enable header versioning.
     *
     * Default: true
     *
     * @var null|bool
     */
    'versioning'         => env('DB_ENCRYPTION_VERSIONING', true),

    /*
     * Control characters used by header.
     *
     * Default: [
     *     'header' => [
     *         'start'      => 1, // or: chr(1)
     *         'stop'       => 4, // or: chr(4)
     *     ],
     *     'prefix' => [
     *         'start'      => 2, // or: chr(2)
     *         'stop'       => 3, // or: chr(3)
     *     ],
     *     'field'   => [
     *         'start'      => 30, // or: chr(30)
     *         'delimiter'  => 25, // or: chr(25)
     *         'stop'       => 23, // or: chr(23)
     *     ],
     * ]
     *
     * @var null|array
     */
    'control_characters' => null,

];
