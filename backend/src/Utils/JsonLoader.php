<?php

declare(strict_types=1);

namespace Yaro\EcommerceProject\Utils;

use Psr\Log\LoggerInterface;
use Exception;

class JsonLoader
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function load(string $filePath): array
    {
        if (!file_exists($filePath)) {
            $this->logger->error("JSON file not found at: $filePath");
            throw new Exception("JSON file not found at: $filePath");
        }

        $jsonData = file_get_contents($filePath);
        if ($jsonData === false) {
            $this->logger->error("Failed to read JSON file at path: $filePath");
            throw new Exception("Failed to read JSON file at path: $filePath");
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error("Invalid JSON data: " . json_last_error_msg());
            throw new Exception("Invalid JSON data: " . json_last_error_msg());
        }

        $this->logger->info("Successfully loaded JSON data from: $filePath");
        return $data;
    }
}
