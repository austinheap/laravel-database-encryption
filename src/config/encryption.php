<?php

return [

    'prefix'     => env('ENCRYPTION_PREFIX', '__ENCRYPTED-%VERSION%__:'),
    'versioning' => env('ENCRYPTION_VERSIONING', true),

];
