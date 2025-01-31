<?php

namespace Yaro\EcommerceProject\Models;

use Yaro\EcommerceProject\Config\Database;
use Psr\Log\LoggerInterface;

abstract class Model
{
    protected static string $table;

    protected function getConnection(): \PDO
    {
        return Database::getConnection();
    }

    protected function executeQuery(string $query, array $params = []): bool
    {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare($query);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    protected function fetchColumn(string $query, array $params = []): ?int
    {
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $id = $stmt->fetchColumn();
            return $id !== false ? (int)$id : null;
        } catch (\PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return null;
        }
    }
}

class TextAttribute extends Model
{
    protected static string $table = 'text_attributes';
    private string $name;
    private int $productId;
    private LoggerInterface $logger;

    public function __construct(string $name, int $productId, LoggerInterface $logger)
    {
        $this->name = $name;
        $this->productId = $productId;
        $this->logger = $logger;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function save(): void
    {
        $query = "
            INSERT INTO " . static::$table . " (product_id, name) 
            VALUES (:product_id, :name)
        ";
        $params = [
            'product_id' => $this->productId,
            'name' => $this->name,
        ];
        if (!$this->executeQuery($query, $params)) {
            $this->logger->error("Error saving TextAttribute: " . print_r($params, true));
        }
    }

    public function saveItem(string $displayValue, string $value): void
    {
        $attributeId = $this->getId();
        if ($attributeId === null) {
            $this->logger->error("Attribute not found for name: {$this->name} and product_id: {$this->productId}");
            return;
        }

        $query = "
            INSERT INTO text_attribute_items (attribute_id, display_value, value)
            VALUES (:attribute_id, :display_value, :value)
        ";
        $params = [
            'attribute_id' => $attributeId,
            'display_value' => $displayValue,
            'value' => $value,
        ];
        if (!$this->executeQuery($query, $params)) {
            $this->logger->error("Error saving TextAttribute item: " . print_r($params, true));
        }
    }

    public function getId(): ?int
    {
        $query = "
            SELECT id FROM " . static::$table . " 
            WHERE name = :name AND product_id = :product_id
        ";
        $params = [
            'name' => $this->name,
            'product_id' => $this->productId,
        ];
        return $this->fetchColumn($query, $params);
    }
}
