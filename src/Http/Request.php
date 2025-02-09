<?php

namespace App\Http;

/**
 * Class Request
 * Handles HTTP request data and provides utility methods for REST API.
 */
class Request
{
    public string $method;
    public string $path;
    public array $segments;
    public array $query_params;
    public array $headers;
    public array $body;
    public array $files;

    public string $controller;
    public string $action;
    public array $params;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $this->getCleanPath();
        $this->segments = array_filter(explode('/', trim($this->path, '/')));
        $this->query_params = $_GET;
        $this->headers = $this->parseHeaders();
        $this->body = $this->parseBody();
        $this->files = $_FILES;
        
        // Extract controller, action, and params
        $this->controller = $this->segments[0] ?? 'home'; // Default controller
        $this->action = $this->segments[1] ?? 'index'; // Default action
        $this->params = array_slice($this->segments, 2); // Remaining parts are params

    }

    /**
     * Get the request URI without script path (index.php).
     */
    private function getCleanPath(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        
        // Remove script directory if it's included in URI
        if ($_SERVER['SCRIPT_NAME'] !== '/' && strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        }

        return $uri ?: '/';
    }

    /**
     * Get a query parameter value or return default.
     */
    public function getQuery(string $key, mixed $default = null): mixed
    {
        return $this->query_params[$key] ?? $default;
    }

    /**
     * Parse request headers.
     */
    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[ucwords(strtolower($name), '-')] = $value;
            }
        }
        return $headers;
    }

    /**
     * Parse request body based on content type.
     */
    private function parseBody(): array
    {
        if ($this->method === 'POST' || $this->method === 'PUT' || $this->method === 'PATCH') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (str_contains($contentType, 'application/json')) {
                return json_decode(file_get_contents('php://input'), true) ?? [];
            }
            return $_POST;
        }
        return [];
    }

    /**
     * Get request bearer token (for API authentication).
     */
    public function getBearerToken(): ?string
    {
        $authHeader = $this->headers['Authorization'] ?? '';
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function dump(): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'method' => $this->method,
            'path' => $this->path,
            'segments' => $this->segments,
            'query_params' => $this->query_params,
            'headers' => $this->headers,
            'body' => $this->body,
            'files' => $this->files,
            'controller' => $this->controller,
            'action' => $this->action,
            'params' => $this->params,
        ], JSON_PRETTY_PRINT);
        exit;
    }

    public function pre_dump(): void
    {
        echo '<pre>' . print_r([
            'method' => $this->method,
            'path' => $this->path,
            'segments' => $this->segments,
            'query_params' => $this->query_params,
            'headers' => $this->headers,
            'body' => $this->body,
            'files' => $this->files,
            'controller' => $this->controller,
            'action' => $this->action,
            'params' => $this->params,
        ], true) . '</pre>';
        exit;
    }

}
