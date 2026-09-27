<?php

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getBody(): string
    {
        return $this->content;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->statusCode = $statusCode;
        $this->setHeader('Location', $url);
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Location: ' . $url);
            exit;
        }
    }

    public function json(array $data, int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $this;
    }

    public function send(): void
    {
        if (headers_sent()) {
            echo $this->content;
            return;
        }

        http_response_code($this->statusCode);

        // Security headers
        $defaultSecurityHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ];

        foreach ($defaultSecurityHeaders as $header => $value) {
            if (!isset($this->headers[$header])) {
                header("{$header}: {$value}");
            }
        }

        foreach ($this->headers as $header => $value) {
            header("{$header}: {$value}");
        }

        echo $this->content;
    }
}
