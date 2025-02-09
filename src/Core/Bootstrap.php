<?php

namespace App\Core;

use App\Http\Request;
use App\Http\Router;

class Bootstrap
{
    protected static array $config = [];

    public static function start(array $config): void
    {
        // Lưu config vào biến static
        self::$config = $config;

        // Khởi tạo Settings
        Settings::init(self::$config);

        // Xử lý request & router
        $request = new Request();
        $router = new Router($request);
        $router->dispatch();
    }

    public static function getConfig(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }
}
