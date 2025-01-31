<?php

namespace Yaro\EcommerceProject\Models;

use Yaro\EcommerceProject\Config\Database;

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

class Product extends Model
{
    protected static string $table = 'products';

    protected string $name;
    protected string $description;
    protected string $brand;
    protected int $categoryId;
    protected bool $inStock;

    public function __construct(string $name, string $description, string $brand, int $categoryId, bool $inStock)
    {
        $this->name = $name;
        $this->description = $description;
        $this->brand = $brand;
        $this->categoryId = $categoryId;
        $this->inStock = $inStock;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function save(): void
    {
        $query = "INSERT INTO " . static::$table . " (name, description, brand, category_id, in_stock)
                  VALUES (:name, :description, :brand, :category_id, :in_stock)
                  ON DUPLICATE KEY UPDATE description = :description, brand = :brand, in_stock = :in_stock";

        $params = [
            'name' => $this->name,
            'description' => $this->description,
            'brand' => $this->brand,
            'category_id' => $this->categoryId,
            'in_stock' => $this->inStock ? 1 : 0,
        ];

        $this->executeQuery($query, $params);
    }

    public function getId(): ?int
    {
        $query = "SELECT id FROM " . static::$table . " WHERE name = :name AND category_id = :category_id";
        $params = [
            'name' => $this->name,
            'category_id' => $this->categoryId,
        ];

        return $this->fetchColumn($query, $params);
    }

    public function saveGalleryImage(string $imageUrl): void
    {
        $query = "INSERT INTO gallery (product_id, image_url) VALUES (:product_id, :image_url)";
        $params = [
            'product_id' => $this->getId(),
            'image_url' => $imageUrl,
        ];

        $this->executeQuery($query, $params);
    }

    public function savePrice(string $currency, string $symbol, float $amount): void
    {
        $query = "INSERT INTO prices (product_id, currency, symbol, amount)
                  VALUES (:product_id, :currency, :symbol, :amount)";

        $params = [
            'product_id' => $this->getId(),
            'currency' => $currency,
            'symbol' => $symbol,
            'amount' => $amount,
        ];

        $this->executeQuery($query, $params);
    }
}
