<?php

namespace Hamlet\Http\Writers;

use Hamlet\Http\Message\Response;
use Hamlet\Http\Message\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class WrapperResponseWriter implements ResponseWriter
{
    use ResponseWriterTrait;

    /** @var int */
    private $statusCode = 200;

    /** @var array<string,array<string>> */
    private $headers = [];

    /** @var StreamInterface|null */
    private $body;

    public function __construct()
    {
        $this->headers['Server'] = ['Hamlet'];
    }

    public function status(int $code, string $line = null): void
    {
        $this->statusCode = $code;
    }

    public function header(string $key, string $value): void
    {
        $this->headers[$key][] = $value;
    }

    public function writeAndEnd(string $payload): void
    {
        $this->body = Stream::fromString($payload);
    }

    public function end(): void
    {
    }

    public function response(): ResponseInterface
    {
        $builder = Response::nonValidatingBuilder()
            ->withStatus($this->statusCode)
            ->withHeaders($this->headers);
        if ($this->body) {
            $builder->withBody($this->body);
        }
        return $builder->build();
    }
}
