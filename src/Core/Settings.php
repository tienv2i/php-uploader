<?php

namespace App\Core;

class Settings
{
    private static ?self $instance = null;
    private array $config = [];

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Initialize settings (should be called once in index.php)
     */
    public static function init(array $config): void
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
    }

    /**
     * Get settings instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \Exception("Settings not initialized.");
        }
        return self::$instance;
    }

    /**
     * Get a specific config value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

}
