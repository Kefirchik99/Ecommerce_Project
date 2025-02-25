<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Models;

use Psr\Log\LoggerInterface;

class SwatchAttribute extends Attribute
{
    protected static string $table = 'swatch_attributes';

    public function __construct(string $name, int $productId, LoggerInterface $logger)
    {
        parent::__construct($name, $productId, $logger);
    }

    public function save(): void
    {
        $db = $this->getConnection();
        try {
            $stmt = $db->prepare("
                INSERT INTO " . static::$table . " (product_id, name)
                VALUES (:product_id, :name)
            ");
            $stmt->execute([
                'product_id' => $this->getProductId(),
                'name' => $this->getName(),
            ]);
            $this->logger->info("Swatch attribute saved with product ID " . $this->getProductId() . " and name " . $this->getName());
        } catch (\PDOException $e) {
            $this->logger->error("Error saving swatch attribute for product ID " . $this->getProductId() . ": " . $e->getMessage());
        }
    }

    public function saveItem(string $displayValue, string $value): void
    {
        $db = $this->getConnection();
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
                'name' => $this->getName(),
                'product_id' => $this->getProductId(),
                'display_value' => $displayValue,
                'value' => $value,
            ]);
            $this->logger->info("Swatch attribute item saved for product ID " . $this->getProductId() . ", name " . $this->getName() . ", display value " . $displayValue . ", value " . $value);
        } catch (\PDOException $e) {
            $this->logger->error("Error saving swatch attribute item for product ID " . $this->getProductId() . ": " . $e->getMessage());
        }
    }
}
