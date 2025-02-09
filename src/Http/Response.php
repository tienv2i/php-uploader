<?php

namespace App\Http;

use App\Core\Settings;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];

    /**
     * Set HTTP status code
     */
    public function setStatus(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set a response header
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Send headers and status code
     */
    protected function sendHeaders(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
    }

    /**
     * Send a JSON response
     */
    public function json(array $data): void
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->sendHeaders();
        echo json_encode($data);
        exit;
    }

    /**
     * Send an HTML response
     */
    public function html(string $content): void
    {
        $this->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->sendHeaders();
        echo $content;
        exit;
    }

    /**
     * Send a plain text response
     */
    public function text(string $content): void
    {
        $this->setHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->sendHeaders();
        echo $content;
        exit;
    }

    /**
     * Redirect to another URL
     */
    public function redirect(string $url, int $status = 302): void
    {
        $this->setStatus($status);
        $this->setHeader('Location', $url);
        $this->sendHeaders();
        exit;
    }

    /**
     * Render a Twig template
     */
    public function render(string $template, array $data = []): void
    {
        $settings = Settings::getInstance();
        $templateDir = $settings->get('template_dir');
    
        $loader = new \Twig\Loader\FilesystemLoader($templateDir);
        $twig = new \Twig\Environment($loader);
        
        // Các thông số mặc định
        $defaultData = [
            'base_url'     => $settings->get('public_dir'),
            'static_url'   => $settings->get('static_dir'),
            'app_name'     => $settings->get('app_name', 'My App'),
            'current_year' => date('Y'),
            'request_uri'  => $_SERVER['REQUEST_URI'] ?? '/',
        ];
    
        // Gộp dữ liệu mặc định với dữ liệu truyền vào
        $data = array_merge($defaultData, $data);
    
        $this->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->sendHeaders();
    
        echo $twig->render($template, $data);
        exit;
    }
    
}
