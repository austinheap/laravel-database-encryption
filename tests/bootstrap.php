<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/DatabaseTestCase.php';
require_once __DIR__ . '/Models/FakeModel.php';
require_once __DIR__ . '/Models/RealModel.php';
require_once __DIR__ . '/Models/DatabaseModel.php';
require_once __DIR__ . '/Models/DummyModel.php';

define('LARAVEL_DATABASE_ENCRYPTION_TESTS', true);
define('LARAVEL_DATABASE_ENCRYPTION_ITERATIONS', 10);
