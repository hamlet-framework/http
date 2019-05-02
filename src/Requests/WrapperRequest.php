<?php

namespace Hamlet\Http\Requests;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class WrapperRequest implements Request
{
    use RequestTrait;

    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * @var string|null
     */
    private $path = null;

    public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    public function getProtocolVersion(): string
    {
        return $this->serverRequest->getProtocolVersion();
    }

    /**
     * @param string $version
     * @return static
     */
    public function withProtocolVersion($version)
    {
        return new self($this->serverRequest->withProtocolVersion($version));
    }

    /**
     * @return string[][]
     */
    public function getHeaders()
    {
        return $this->serverRequest->getHeaders();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return $this->serverRequest->hasHeader($name);
    }

    /**
     * @param string $name
     * @return string[]
     */
    public function getHeader($name)
    {
        return $this->serverRequest->getHeader($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        return $this->serverRequest->getHeaderLine($name);
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return static
     * @throws InvalidArgumentException
     */
    public function withHeader($name, $value)
    {
        return new self($this->serverRequest->withHeader($name, $value));
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return static
     * @throws InvalidArgumentException
     */
    public function withAddedHeader($name, $value)
    {
        return new self($this->serverRequest->withAddedHeader($name, $value));
    }

    /**
     * @param string $name
     * @return static
     */
    public function withoutHeader($name)
    {
        return new self($this->serverRequest->withoutHeader($name));
    }

    /**
     * @return StreamInterface
     */
    public function getBody()
    {
        return $this->serverRequest->getBody();
    }

    /**
     * @param StreamInterface $body
     * @return static
     * @throws InvalidArgumentException
     */
    public function withBody(StreamInterface $body)
    {
        return new self($this->serverRequest->withBody($body));
    }

    /**
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->serverRequest->getRequestTarget();
    }

    /**
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        return new self($this->serverRequest->withRequestTarget($requestTarget));
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->serverRequest->getMethod();
    }

    /**
     * @param string $method
     * @return static
     * @throws InvalidArgumentException
     */
    public function withMethod($method)
    {
        return new self($this->serverRequest->withMethod($method));
    }

    /**
     * @return UriInterface
     */
    public function getUri()
    {
        return $this->serverRequest->getUri();
    }

    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        return new self($this->serverRequest->withUri($uri, $preserveHost));
    }

    public function getPath(): string
    {
        if ($this->path === null) {
            $this->path = $this->serverRequest->getUri()->getPath();
        }
        return $this->path;
    }

    public function getQueryParams(): array
    {
        return $this->serverRequest->getQueryParams();
    }

    public function getParsedBody()
    {
        return $this->serverRequest->getParsedBody();
    }

    /**
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverRequest->getServerParams();
    }

    /**
     * @return array
     */
    public function getCookieParams()
    {
        return $this->serverRequest->getCookieParams();
    }

    /**
     * @param array $cookies
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        return new self($this->serverRequest->withCookieParams($cookies));
    }

    /**
     * @param array $query
     * @return static
     */
    public function withQueryParams(array $query)
    {
        return new self($this->serverRequest->withQueryParams($query));
    }

    /**
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->serverRequest->getUploadedFiles();
    }

    /**
     * @param array $uploadedFiles
     * @return static
     * @throws InvalidArgumentException
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        return new self($this->serverRequest->withUploadedFiles($uploadedFiles));
    }

    /**
     * @param null|array|object $data
     * @return static
     * @throws InvalidArgumentException
     */
    public function withParsedBody($data)
    {
        return new self($this->serverRequest->withParsedBody($data));
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->serverRequest->getAttributes();
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->serverRequest->getAttribute($name, $default);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function withAttribute($name, $value)
    {
        return new self($this->serverRequest->withAttribute($name, $value));
    }

    /**
     * @param string $name
     * @return static
     */
    public function withoutAttribute($name)
    {
        return new self($this->serverRequest->withoutAttribute($name));
    }
}
