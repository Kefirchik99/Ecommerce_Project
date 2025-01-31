<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Models;

use Yaro\EcommerceProject\Config\Database;
use Psr\Log\LoggerInterface;
use PDO;
use PDOException;

abstract class Model
{
    protected static string $table;
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function find(int $id, LoggerInterface $logger): ?array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $logger->info("Record found in table " . static::$table . " with ID {$id}");
            } else {
                $logger->info("No record found in table " . static::$table . " with ID {$id}");
            }

            return $result ?: null;
        } catch (PDOException $e) {
            $logger->error("Database error in table " . static::$table . ": " . $e->getMessage());
            return null;
        }
    }

    protected function executeQuery(string $query, array $params = []): bool
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            $this->logger->error("Query Execution Error: " . $e->getMessage());
            return false;
        }
    }

    protected function fetchColumn(string $query, array $params = []): ?string
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchColumn() ?: null;
        } catch (PDOException $e) {
            $this->logger->error("Column Fetch Error: " . $e->getMessage());
            return null;
        }
    }
}
