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

    public function getHeader(string $name): ?string
    {
        foreach ($this->headers as $key => $val) {
            if (strcasecmp($key, $name) === 0) {
                return $val;
            }
        }
        $defaults = $this->getDefaultSecurityHeaders();
        foreach ($defaults as $key => $val) {
            if (strcasecmp($key, $name) === 0) {
                return $val;
            }
        }
        return null;
    }

    /**
     * Generate standard security headers including CSP and conditional HSTS.
     * 
     * Note: Inline scripts and styles are currently permitted via 'unsafe-inline'
     * as required by existing views and dynamic rendering components.
     * TODO: Refactor inline JavaScript/CSS out to external bundles or adopt nonce-based CSP.
     */
    public function getDefaultSecurityHeaders(): array
    {
        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://accounts.google.com https://apis.google.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "img-src 'self' data: blob: https://accounts.google.com https://*.googleusercontent.com https://api.dicebear.com",
            "font-src 'self' data: https://cdnjs.cloudflare.com https://fonts.gstatic.com",
            "connect-src 'self' https://accounts.google.com https://apis.google.com https://cdnjs.cloudflare.com",
            "frame-src 'self' https://accounts.google.com",
            "worker-src 'self' blob: https://cdnjs.cloudflare.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https://accounts.google.com",
        ];

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Content-Security-Policy' => implode('; ', $cspDirectives),
        ];

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        if ($isHttps) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        return $headers;
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
        $defaultSecurityHeaders = $this->getDefaultSecurityHeaders();
        foreach ($defaultSecurityHeaders as $header => $value) {
            if (!isset($this->headers[$header])) {
                $this->headers[$header] = $value;
            }
        }

        if (headers_sent()) {
            echo $this->content;
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $header => $value) {
            header("{$header}: {$value}");
        }

        echo $this->content;
    }
}
