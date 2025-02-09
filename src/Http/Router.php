<?php

namespace App\Http;

class Router
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Convert controller name to CamelCase (supports kebab-case and snake_case).
     */
    private function formatControllerName(string $controller): string
    {
        $formatted = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $controller)));
        return $formatted;
    }


    public function dispatch(): void
    {
        $controllerClass = 'App\\Controllers\\' . $this->formatControllerName($this->request->controller);
        $action = $this->request->action;
        
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass($this->request);

            if (method_exists($controllerInstance, $action)) {
                call_user_func([$controllerInstance, $action], ...$this->request->params);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

    public function pre_dump(): void
    {
        echo '<pre>' . print_r([
            'controller' => $this->formatControllerName($this->request->controller),
            'action' => $this->request->action,
            'params' => $this->request->params,
            'full_request' => [
                'method' => $this->request->method,
                'path' => $this->request->path,
                'segments' => $this->request->segments,
                'query_params' => $this->request->query_params,
            ],
        ], true) . '</pre>';
        exit;
    }

}
