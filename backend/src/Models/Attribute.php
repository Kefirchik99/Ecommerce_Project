<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Models;

use Psr\Log\LoggerInterface;

abstract class Attribute extends Model
{
    protected static string $table = 'attributes';

    protected string $name;
    protected int $productId;

    public function __construct(string $name, int $productId, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->name = $name;
        $this->productId = $productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    abstract public function save(): void;

    public function getId(): ?int
    {
        $query = "SELECT id FROM " . static::$table . " WHERE name = :name AND product_id = :productId";
        $params = [
            'name' => $this->name,
            'productId' => $this->productId,
        ];

        return (int) $this->fetchColumn($query, $params);
    }

    public function saveItem(string $displayValue, string $value): void
    {
        $attributeId = $this->getId();

        if ($attributeId === null) {
            $this->logger->error("Failed to save item: Attribute ID is null for name '{$this->name}'.");
            return;
        }

        $query = "INSERT INTO attribute_items (attribute_id, display_value, value)
                  VALUES (:attribute_id, :display_value, :value)";

        $params = [
            'attribute_id' => $attributeId,
            'display_value' => $displayValue,
            'value' => $value,
        ];

        if (!$this->executeQuery($query, $params)) {
            $this->logger->error("Failed to save attribute item for Attribute ID {$attributeId}.");
        }
    }
}
