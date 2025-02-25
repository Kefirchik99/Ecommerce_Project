<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Models;

use Psr\Log\LoggerInterface;

class Category extends Model
{
    protected static string $table = 'categories';
    private string $name;

    public function __construct(string $name, LoggerInterface $logger)
    {
        parent::__construct($logger);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function save(): void
    {
        $this->executeQuery(
            "INSERT INTO " . static::$table . " (name) VALUES (:name)",
            ['name' => $this->name]
        );
    }

    public function getId(): ?int
    {
        return $this->fetchColumn(
            "SELECT id FROM " . static::$table . " WHERE name = :name",
            ['name' => $this->name]
        );
    }

    public static function findByName(string $name): ?array
    {
        return self::findByField('name', $name);
    }
}
