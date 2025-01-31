<?php

namespace Yaro\EcommerceProject\Models;

class ElectronicsProduct extends Product
{
    protected static string $table = 'electronics_products';

    private array $galleryImages = [];
    private array $prices = [];

    public function save(): void
    {
        $db = $this->getConnection();
        $stmt = $db->prepare("
            INSERT INTO " . static::$table . " (name, description, brand, category_id, in_stock)
            VALUES (:name, :description, :brand, :category_id, :in_stock)
            ON DUPLICATE KEY UPDATE description = :description, brand = :brand, in_stock = :in_stock
        ");
        $stmt->execute([
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'brand' => $this->getBrand(),
            'category_id' => $this->getCategoryId(),
            'in_stock' => $this->isInStock() ? 1 : 0,
        ]);
    }

    public function addGalleryImage(string $imageUrl): void
    {
        $this->galleryImages[] = $imageUrl;
    }

    public function saveGallery(): void
    {
        $db = $this->getConnection();
        foreach ($this->galleryImages as $imageUrl) {
            $stmt = $db->prepare("
                INSERT INTO gallery (product_id, image_url) 
                VALUES (:product_id, :image_url)
            ");
            $stmt->execute([
                'product_id' => $this->getId(),
                'image_url' => $imageUrl,
            ]);
        }
    }

    public function addPrice(string $currency, string $symbol, float $amount): void
    {
        $this->prices[] = [
            'currency' => $currency,
            'symbol' => $symbol,
            'amount' => $amount,
        ];
    }

    public function savePrices(): void
    {
        $db = $this->getConnection();
        foreach ($this->prices as $price) {
            $stmt = $db->prepare("
                INSERT INTO prices (product_id, currency, symbol, amount)
                VALUES (:product_id, :currency, :symbol, :amount)
            ");
            $stmt->execute([
                'product_id' => $this->getId(),
                'currency' => $price['currency'],
                'symbol' => $price['symbol'],
                'amount' => $price['amount'],
            ]);
        }
    }

    public function format(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'brand' => $this->getBrand(),
            'category' => 'Electronics',
            'inStock' => $this->isInStock(),
        ];
    }
}
