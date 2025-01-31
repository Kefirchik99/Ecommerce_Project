<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject;

require_once __DIR__ . '/vendor/autoload.php';

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Yaro\EcommerceProject\Config\Database;

try {
    $db = Database::getConnection();

    $logger = new Logger('app_logger');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log', Level::Debug));

    $GLOBALS['logger'] = $logger;
} catch (\Exception $e) {
    $logger = new Logger('app_logger');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log', Level::Debug));
    $logger->error("Bootstrap error: " . $e->getMessage());
}
