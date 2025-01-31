<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\GraphQL\Resolvers;

use Yaro\EcommerceProject\Config\Database;
use Psr\Log\LoggerInterface;
use PDO;
use PDOException;
use RuntimeException;

class CategoryResolver
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function resolveAll(): array
    {
        $db = Database::getConnection();

        try {
            $stmt = $db->query("SELECT * FROM categories");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $this->logger->info("Categories fetched", $categories);
            return $categories;
        } catch (PDOException $e) {
            $this->logger->error("Error fetching categories: " . $e->getMessage());
            throw new RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function resolveById(int $id): array
    {
        $db = Database::getConnection();

        try {
            $stmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$category) {
                throw new RuntimeException("Category not found");
            }

            $this->logger->info("Category fetched for ID {$id}", $category);
            return $category;
        } catch (PDOException $e) {
            $this->logger->error("Error fetching category with ID {$id}: " . $e->getMessage());
            throw new RuntimeException("Database error: " . $e->getMessage());
        }
    }
}
