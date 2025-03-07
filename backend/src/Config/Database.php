<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
                $dotenv->safeLoad();
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $port = $_ENV['DB_PORT'] ?? '3306';
                $dbname = $_ENV['DB_NAME'] ?? 'database';
                $user = $_ENV['DB_USER'] ?? 'root';
                $password = $_ENV['DB_PASSWORD'] ?? '';
                $sslCaPath = $_ENV['DB_SSL_CA'] ?? '/etc/ssl/certs/aiven-ca.pem';
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ];
                if (!empty($sslCaPath) && file_exists($sslCaPath)) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCaPath;
                    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
                    $options[PDO::MYSQL_ATTR_SSL_CIPHER] = 'ECDHE-RSA-AES128-GCM-SHA256';
                }
                error_log("SSL Debug: Path={$sslCaPath}, Exists=" . (file_exists($sslCaPath) ? 'Yes' : 'No'));
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4;ssl-mode=REQUIRED";
                self::$instance = new PDO($dsn, $user, $password, $options);
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
