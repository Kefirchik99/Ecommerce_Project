<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Models;

use Yaro\EcommerceProject\Config\Database;
use Psr\Log\LoggerInterface;

class SwatchAttribute extends Model
{
    protected static string $table = 'swatch_attributes';
    private string $name;
    private int $productId;

    public function __construct(string $name, int $productId, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->name = $name;
        $this->productId = $productId;
    }

    public function save(): void
    {
        $db = Database::getConnection();
        try {
            $stmt = $db->prepare("
                INSERT INTO " . static::$table . " (product_id, name) 
                VALUES (:product_id, :name)
            ");
            $stmt->execute([
                'product_id' => $this->productId,
                'name' => $this->name,
            ]);

            $this->logger->info("Swatch attribute saved with product ID {$this->productId} and name {$this->name}");
        } catch (\PDOException $e) {
            $this->logger->error("Error saving swatch attribute for product ID {$this->productId}: " . $e->getMessage());
        }
    }

    public function saveItem(string $displayValue, string $value): void
    {
        $db = Database::getConnection();
        try {
            $stmt = $db->prepare("
                INSERT INTO swatch_attribute_items (attribute_id, display_value, value)
                VALUES (
                    (SELECT id FROM " . static::$table . " WHERE name = :name AND product_id = :product_id LIMIT 1),
                    :display_value,
                    :value
                )
            ");
            $stmt->execute([
                'name' => $this->name,
                'product_id' => $this->productId,
                'display_value' => $displayValue,
                'value' => $value,
            ]);

            $this->logger->info("Swatch attribute item saved for product ID {$this->productId}, name {$this->name}, display value {$displayValue}, value {$value}");
        } catch (\PDOException $e) {
            $this->logger->error("Error saving swatch attribute item for product ID {$this->productId}: " . $e->getMessage());
        }
    }
}
