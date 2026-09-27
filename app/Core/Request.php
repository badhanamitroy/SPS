<?php

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $queryParams;
    private array $bodyParams;
    private array $headers;

    public function __construct(?string $method = null, ?string $uri = null, array $queryParams = [], array $bodyParams = [])
    {
        $this->method = strtoupper($method ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $this->uri = $uri ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?: '/';
        if (empty($queryParams) && ($queryString = parse_url($this->uri, PHP_URL_QUERY))) {
            parse_str($queryString, $parsedQuery);
            $this->queryParams = $parsedQuery;
        } else {
            $this->queryParams = !empty($queryParams) ? $queryParams : ($_GET ?? []);
        }
        $this->bodyParams = !empty($bodyParams) ? $bodyParams : ($_POST ?? []);
        $this->headers = function_exists('getallheaders') ? (getallheaders() ?: []) : [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getQuery(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function getPost(string $key, $default = null)
    {
        return $this->bodyParams[$key] ?? $default;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $this->bodyParams[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }

    public function getSegments(): array
    {
        $trimmed = trim($this->path, '/');
        return $trimmed === '' ? [] : explode('/', $trimmed);
    }

    public function getHeader(string $name, $default = null): ?string
    {
        foreach ($this->headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }
        return $default;
    }

    public function isAjax(): bool
    {
        return strtolower((string)$this->getHeader('X-Requested-With', '')) === 'xmlhttprequest';
    }
}
