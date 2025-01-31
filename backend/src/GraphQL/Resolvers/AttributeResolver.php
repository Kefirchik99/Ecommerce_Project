<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\GraphQL\Resolvers;

use Yaro\EcommerceProject\Config\Database;
use Psr\Log\LoggerInterface;
use PDO;
use PDOException;

class AttributeResolver
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function resolveAttributes(string $productId): array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT id, name, type 
                FROM attributes 
                WHERE product_id = :product_id
            ");
            $stmt->execute(['product_id' => $productId]);

            $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            foreach ($attributes as &$attr) {
                $attr['items'] = $this->resolveItemsForAttribute((int) $attr['id']);
            }

            $this->logger->info("Attributes fetched for product ID {$productId}", $attributes);
            return $attributes;
        } catch (PDOException $e) {
            $this->logger->error("Database error in resolveAttributes for product ID {$productId}: " . $e->getMessage());
            return [];
        }
    }

    public function resolveItemsForAttribute(int $attributeId): array
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT id, display_value AS displayValue, value
                FROM attribute_items
                WHERE attribute_id = :attribute_id
            ");
            $stmt->execute(['attribute_id' => $attributeId]);

            $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $this->logger->info("Attribute items fetched for attribute ID {$attributeId}", $items);
            return $items;
        } catch (PDOException $e) {
            $this->logger->error("Database error in resolveItemsForAttribute for attribute ID {$attributeId}: " . $e->getMessage());
            return [];
        }
    }
}
