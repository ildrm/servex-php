<?php

namespace Servex\Core\Config;

class ConfigManager
{
    private static array $config = [];

    public static function load(string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Config file not found: {$configPath}");
        }
        
        self::$config = require $configPath;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    public static function all(): array
    {
        return self::$config;
    }
} 