<?php

declare(strict_types=1);

require '/var/www/html/vendor/autoload.php';

use Yaro\EcommerceProject\Config\Database;
use Psr\Log\LoggerInterface;

function populateGalleryFromJson(string $jsonFilePath, LoggerInterface $logger): void
{
    $db = Database::getConnection();
    $jsonData = json_decode(file_get_contents($jsonFilePath), true);

    if (!is_array($jsonData)) {
        $logger->error('Invalid JSON file.');
        return;
    }

    foreach ($jsonData as $product) {
        if (empty($product['gallery'])) {
            continue;
        }

        foreach ($product['gallery'] as $imageUrl) {
            try {
                $stmt = $db->prepare("
                    INSERT IGNORE INTO gallery (product_id, image_url) 
                    VALUES (:product_id, :image_url)
                ");
                $stmt->execute([
                    'product_id' => $product['id'],
                    'image_url'  => $imageUrl,
                ]);

                $logger->info("Inserted gallery image for product ID {$product['id']}: $imageUrl");
            } catch (\PDOException $e) {
                $logger->error("Error inserting gallery for product_id {$product['id']}: " . $e->getMessage());
            }
        }
    }

    $logger->info('Gallery table populated successfully.');
}

$logger = $GLOBALS['logger'] ?? null;

if (!$logger) {
    echo 'Logger not initialized.';
    exit;
}

populateGalleryFromJson('/var/www/html/backend/data/data.json', $logger);
