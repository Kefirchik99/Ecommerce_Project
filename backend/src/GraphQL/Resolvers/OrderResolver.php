<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\GraphQL\Resolvers;

use Yaro\EcommerceProject\Config\Database;
use Psr\Log\LoggerInterface;
use RuntimeException;
use PDO;
use PDOException;

class OrderResolver
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createOrder(array $products): string
    {
        if (empty($products)) {
            throw new RuntimeException("Cannot create an empty order.");
        }

        $db = Database::getConnection();

        try {
            $db->beginTransaction();

            $total = array_reduce($products, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

            $stmt = $db->prepare("INSERT INTO orders (total) VALUES (:total)");
            $stmt->execute(['total' => $total]);
            $orderId = $db->lastInsertId();

            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (:order_id, :product_id, :quantity, :price)
            ");

            foreach ($products as $item) {
                $stmt->execute([
                    'order_id'  => $orderId,
                    'product_id' => (string) $item['productId'],
                    'quantity'   => (int) $item['quantity'],
                    'price'      => (float) $item['price'],
                ]);
            }

            $db->commit();
            return "Order #$orderId created successfully!";
        } catch (PDOException $e) {
            $db->rollBack();
            $this->logger->error("Failed to create order: " . $e->getMessage());
            throw new RuntimeException("Failed to create order: " . $e->getMessage());
        }
    }

    public function resolveAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT o.id AS id, o.total, o.created_at, 
                   JSON_ARRAYAGG(
                       JSON_OBJECT('productId', oi.product_id, 'quantity', oi.quantity, 'price', oi.price)
                   ) AS items
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
        ");

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(function ($order) {
            return [
                'id' => $order['id'],
                'total' => $order['total'],
                'created_at' => $order['created_at'],
                'items' => json_decode($order['items'], true) ?? []
            ];
        }, $orders);
    }
}
