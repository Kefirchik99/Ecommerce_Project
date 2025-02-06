<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject;

require_once __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Yaro\EcommerceProject\Config\Database;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = Database::getConnection();

    $logger = new Logger('app_logger');
    $logger->pushHandler(new NullHandler());

    $GLOBALS['logger'] = $logger;
} catch (\Exception $e) {
    $logger = new Logger('app_logger');
    $logger->pushHandler(new NullHandler());
}
