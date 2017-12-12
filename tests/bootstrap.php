<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/DummyModel.php';

define('LARAVEL_DATABASE_ENCRYPTION_TESTS', true);
define('LARAVEL_DATABASE_ENCRYPTION_ITERATIONS', 100);
