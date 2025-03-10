<?php

namespace Servex\Http;

class Request
{
    private array $query;
    private array $parsedBody;
    private array $attributes;
    private array $headers;
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->query = $_GET;
        $this->parsedBody = $this->parseBody();
        $this->attributes = [];
        $this->headers = getallheaders();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    private function parseBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $content = file_get_contents('php://input');
            return json_decode($content, true) ?? [];
        }
        
        return $_POST;
    }

    public function getParsedBody(): array
    {
        return $this->parsedBody;
    }

    public function getQueryParams(): array
    {
        return $this->query;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function withAttribute(string $key, mixed $value): self
    {
        $clone = clone $this;
        $clone->attributes[$key] = $value;
        return $clone;
    }
}
