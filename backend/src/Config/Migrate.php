<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Config;

$autoloadPaths = [
    __DIR__ . '/../../../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

require_once __DIR__ . '/Database.php';

use PDO;
use PDOException;
use Exception;

class Migrate
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function run(): void
    {
        try {
            echo "Running migrations...\n";

            $schemaFile = realpath(__DIR__ . '/../../db/schema.sql');
            if (!file_exists($schemaFile)) {
                throw new Exception("schema.sql not found at: $schemaFile");
            }

            $schemaSQL = file_get_contents($schemaFile);
            $this->db->exec($schemaSQL);

            echo "Database migrations applied successfully.\n";
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage() . "\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new Migrate();
$migration->run();
