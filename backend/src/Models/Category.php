<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Models;

use Yaro\EcommerceProject\Config\Database;
use PDO;
use PDOException;

abstract class Model
{
    protected static string $table;

    protected function getConnection(): PDO
    {
        return Database::getConnection();
    }

    protected function executeQuery(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->getConnection()->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    protected function fetchColumn(string $query, array $params = []): ?int
    {
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute($params);
            $id = $stmt->fetchColumn();
            return $id !== false ? (int)$id : null;
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return null;
        }
    }

    protected static function findByField(string $field, string $value): ?array
    {
        try {
            $stmt = Database::getConnection()->prepare(
                "SELECT * FROM " . static::$table . " WHERE {$field} = :value"
            );
            $stmt->execute(['value' => $value]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return null;
        }
    }
}

class Category extends Model
{
    protected static string $table = 'categories';
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function save(): void
    {
        $this->executeQuery(
            "INSERT INTO " . static::$table . " (name) VALUES (:name)",
            ['name' => $this->name]
        );
    }

    public function getId(): ?int
    {
        return $this->fetchColumn(
            "SELECT id FROM " . static::$table . " WHERE name = :name",
            ['name' => $this->name]
        );
    }

    public static function findByName(string $name): ?array
    {
        return self::findByField('name', $name);
    }
}
