<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Models;

use Psr\Log\LoggerInterface;

class TextAttribute extends Attribute
{
    protected static string $table = 'text_attributes';

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
        } catch (\PDOException $e) {
            $this->logger->error("Error saving TextAttribute: " . $e->getMessage());
        }
    }

    public function saveItem(string $displayValue, string $value): void
    {
        $attributeId = $this->getId();
        if ($attributeId === null) {
            $this->logger->error("Attribute not found for name: " . $this->getName() . " and product_id: " . $this->getProductId());
            return;
        }
        try {
            $stmt = $this->getConnection()->prepare("
                INSERT INTO text_attribute_items (attribute_id, display_value, value)
                VALUES (:attribute_id, :display_value, :value)
            ");
            $stmt->execute([
                'attribute_id' => $attributeId,
                'display_value' => $displayValue,
                'value' => $value,
            ]);
        } catch (\PDOException $e) {
            $this->logger->error("Error saving TextAttribute item: " . $e->getMessage());
        }
    }

    public function getId(): ?int
    {
        $query = "
            SELECT id FROM " . static::$table . " 
            WHERE name = :name AND product_id = :product_id
        ";
        $params = [
            'name' => $this->getName(),
            'product_id' => $this->getProductId(),
        ];
        return $this->fetchColumn($query, $params);
    }
}
