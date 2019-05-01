<?php

namespace Hamlet\Http\Writers;

use Hamlet\Http\Message\Response;
use Hamlet\Http\Message\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class WrapperResponseWriter implements ResponseWriter
{
    use ResponseWriterTrait;

    /**
     * @var int
     */
    private $statusCode = 200;

    /**
     * @var array
     * @psalm-var array<string,array<string>>
     */
    private $headers = [];

    /**
     * @var StreamInterface|null
     */
    private $body;

    public function __construct()
    {
        $this->headers['Server'] = ['Hamlet'];
    }

    /**
     * @param int $code
     * @param string|null $line
     * @return void
     */
    public function status(int $code, string $line = null)
    {
        $this->statusCode = $code;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function header(string $key, string $value)
    {
        $this->headers[$key][] = $value;
    }

    /**
     * @param string $payload
     * @return void
     */
    public function writeAndEnd(string $payload)
    {
        $this->body = Stream::fromString($payload);
    }

    /**
     * @return void
     */
    public function end()
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
