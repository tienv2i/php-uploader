<?php
class Router {
    public string $path;
    public array $segments = [];
    private array $routes = [];
    private ?array $matchedRoute = null;

    public function __construct() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $script_path = $_SERVER['SCRIPT_NAME'];

        // Remove request path from uri
        if (strpos($path, $script_path) == 0) {
            $path = substr($path, strlen($script_path));
        }

        $this->segments = array_filter(explode('/', trim($path, '/')));

        $this->path = '/' . implode('/', $this->segments);

    }

    public function get(string $pattern, callable $callback): self {
        $this->addRoute('GET', $pattern, $callback);
        return $this;
    }

    public function post(string $pattern, callable $callback): self {
        $this->addRoute('POST', $pattern, $callback);
        return $this;
    }

    private function addRoute(string $method, string $pattern, callable $callback): void {
        $this->routes[] = [
            'method' => $method,
            'pattern' => "~^" . $pattern . "$~",
            'callback' => $callback
        ];
    }

    public function dispatch(): void {
        $requestUri = $this->path;
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {

            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $requestUri, $matches)) {
                array_shift($matches);
                $this->matchedRoute = [
                    'method' => $route['method'],
                    'pattern' => $route['pattern'],
                    'matches' => $matches
                ];
                call_user_func($route['callback'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    public function dump(): self {
        echo "<pre>";
        echo "<strong>Router Debug Information:</strong>\n";
        echo "<strong>Request Path:</strong> " . htmlspecialchars($this->path) . "\n";
        echo "<strong>Segments:</strong> " . json_encode($this->segments, JSON_PRETTY_PRINT) . "\n";
        echo "<strong>Registered Routes:</strong>\n";
        foreach ($this->routes as $route) {
            echo " - Method: " . $route['method'] . ", Pattern: " . $route['pattern'] . "\n";
        }
        echo "<strong>Route Match Status:</strong> ";
        if ($this->matchedRoute) {
            echo "Matched\n";
            echo "<strong>Matched Route:</strong> " . json_encode($this->matchedRoute, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "No Match\n";
        }
        echo "</pre>";
        return $this;
    }
}
