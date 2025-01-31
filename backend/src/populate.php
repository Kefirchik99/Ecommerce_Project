<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use Yaro\EcommerceProject\Utils\JsonLoader;
use Yaro\EcommerceProject\Utils\DatabaseSeeder;
use Exception;

$logger = $GLOBALS['logger'] ?? null;

if (!$logger) {
    die("Logger not initialized.\n");
}

try {
    $dataFile = realpath(__DIR__ . '/../data/data.json');

    if (!$dataFile || !file_exists($dataFile)) {
        throw new Exception("File not found or inaccessible: " . ($dataFile ?? 'Invalid path'));
    }

    $logger->info("Resolved data file path: $dataFile");

    $jsonLoader = new JsonLoader($logger);
    $payload = $jsonLoader->load($dataFile);

    if (!isset($payload['data'])) {
        throw new Exception("Invalid JSON structure: Missing 'data' key at top level.");
    }

    $databaseSeeder = new DatabaseSeeder($logger);
    $databaseSeeder->seed($payload['data']);

    $logger->info("Database populated successfully.");
} catch (Exception $e) {
    $logger->error("Error during database seeding: " . $e->getMessage());
    echo "Error during database seeding: " . $e->getMessage() . "\n";
}
