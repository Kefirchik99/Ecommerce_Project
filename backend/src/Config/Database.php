<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=mysql-container;dbname=scandiweb;charset=utf8mb4',
                    'root',
                    'root_password',
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                die('Database connection failed.');
            }
        }

        return self::$instance;
    }

    public static function closeConnection(): void
    {
        self::$instance = null;
    }
}
