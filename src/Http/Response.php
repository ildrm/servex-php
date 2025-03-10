<?php

namespace Servex\Http;

class Response
{
    private int $statusCode;
    private array $headers;
    private mixed $content;

    public function __construct(mixed $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $status;
        $this->headers = array_merge([
            'Content-Type' => 'application/json'
        ], $headers);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        if (is_array($this->content) || is_object($this->content)) {
            echo json_encode($this->content);
        } else {
            echo $this->content;
        }
    }

    public function withStatus(int $code): self
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        return $clone;
    }

    public function withHeader(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }

    public function withJson(mixed $data): self
    {
        $clone = clone $this;
        $clone->content = $data;
        $clone->headers['Content-Type'] = 'application/json';
        return $clone;
    }
}
