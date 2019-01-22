<?php

namespace Hamlet\Http\Requests;

use Hamlet\Http\Message\ServerRequest;
use Hamlet\Http\Message\Stream;
use Hamlet\Http\Message\Uri;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class DefaultRequest extends ServerRequest implements Request
{
    use RequestTrait;

    /** @var string */
    protected $path;

    protected const HEADER_ALIASES = [
        'CONTENT_TYPE'                   => 'Content-Type',
        'CONTENT_LENGTH'                 => 'Content-Length',
        'CONTENT_MD5'                    => 'Content-MD5',
        'REDIRECT_HTTP_AUTHORIZATION'    => 'Authorization',
        'PHP_AUTH_DIGEST'                => 'Authorization',
        'HTTP_HOST'                      => 'Host',
        'HTTP_CONNECTION'                => 'Connection',
        'HTTP_CACHE_CONTROL'             => 'Cache-Control',
        'HTTP_UPGRADE_INSECURE_REQUESTS' => 'Upgrade-Insecure-Requests',
        'HTTP_USER_AGENT'                => 'User-Agent',
        'HTTP_DNT'                       => 'DNT',
        'HTTP_ACCEPT'                    => 'Accept',
        'HTTP_ACCEPT_ENCODING'           => 'Accept-Encoding',
        'HTTP_ACCEPT_LANGUAGE'           => 'Accept-Language',
        'HTTP_COOKIE'                    => 'Cookie'
    ];

    /**
     * @psalm-suppress MixedTypeCoercion
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     */
    public function __construct()
    {
        parent::__construct();

        $this->method       = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->serverParams = $_SERVER ?? [];
        $this->cookieParams = $_COOKIE ?? [];
        $this->queryParams  = $_GET ?? [];
        $this->parsedBody   = $_POST ?? [];
        $this->path         = strtok($_SERVER['REQUEST_URI'] ?? '', '?') ?: '';

        $this->uriGenerator = function (): UriInterface {
            return self::readUriFromServerParams($_SERVER);
        };
        $this->protocolVersionGenerator = function (): string {
            return self::readVersionFromServerParams($_SERVER);
        };
        $this->headersGenerator = function (): array {
            return self::readHeadersFromServerParams($_SERVER);
        };
        $this->bodyGenerator = function (): StreamInterface {
            return self::readBodyFromInputStream('php://input');
        };
        $this->uploadedFilesGenerator = function (): array {
            return self::readUploadedFilesFromFileParams($_FILES);
        };
    }

    public function getPath(): string
    {
        if (!isset($this->path)) {
            $this->path = $this->getUri()->getPath();
        }
        return $this->path;
    }

    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return DefaultRequest
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $copy = parent::withUri($uri, $preserveHost);
        assert($copy instanceof DefaultRequest);
        $copy->path = $uri->getPath();
        return $copy;
    }

    /**
     * Get a Uri populated with values from server params.
     * @param array<string,string> $serverParams
     * @return UriInterface
     */
    protected static function readUriFromServerParams(array $serverParams): UriInterface
    {
        $builder = Uri::nonValidatingBuilder();

        $builder->withScheme(!empty($serverParams['HTTPS']) && $serverParams['HTTPS'] !== 'off' ? 'https' : 'http');

        $hasPort = false;
        if (isset($serverParams['HTTP_HOST'])) {
            $hostHeaderParts = explode(':', (string) $serverParams['HTTP_HOST'], 2);
            $builder->withHost($hostHeaderParts[0]);
            if (count($hostHeaderParts) > 1) {
                $hasPort = true;
                $builder->withPort((int) $hostHeaderParts[1]);
            }
        } elseif (isset($serverParams['SERVER_NAME'])) {
            $builder->withHost((string) $serverParams['SERVER_NAME']);
        } elseif (isset($serverParams['SERVER_ADDR'])) {
            $builder->withHost((string) $serverParams['SERVER_ADDR']);
        }

        if (!$hasPort && isset($serverParams['SERVER_PORT'])) {
            $builder->withPort((int) $serverParams['SERVER_PORT']);
        }

        $hasQuery = false;
        if (isset($serverParams['REQUEST_URI'])) {
            $requestUriParts = explode('?', (string) $serverParams['REQUEST_URI'], 2);
            $builder->withPath($requestUriParts[0]);
            if (count($requestUriParts) > 1) {
                $hasQuery = true;
                $builder->withQuery($requestUriParts[1]);
            }
        }

        if (!$hasQuery && isset($serverParams['QUERY_STRING'])) {
            $builder->withQuery((string) $serverParams['QUERY_STRING']);
        }

        return $builder->build();
    }

    /**
     * @param array<string,string> $serverParams
     * @return array<string,array<int,string>>
     */
    protected static function readHeadersFromServerParams(array $serverParams): array
    {
        if (\function_exists('getallheaders')) {
            $headers = [];
            /** @psalm-suppress MixedAssignment */
            foreach (\getallheaders() as $name => $value) {
                $headers[(string) $name] = [(string) $value];
            }
            return $headers;
        }

        $headers = [];
        if (isset($serverParams['HTTP_HOST'])) {
            $headers['Host'] = [$serverParams['HTTP_HOST']];
        }
        foreach ($serverParams as $name => &$value) {
            if ($name === 'HTTP_HOST') {
                continue;
            }
            if (isset(self::HEADER_ALIASES[$name])) {
                $alias = (string) self::HEADER_ALIASES[$name];
                $headers[$alias][] = $value;
            } elseif (\substr($name, 0, 5) == "HTTP_") {
                $headerName = \str_replace(' ', '-', \ucwords(\strtolower(\str_replace('_', ' ', \substr($name, 5)))));
                $headers[$headerName][] = $value;
            }
        }
        if (!isset($headers['Authorization']) && isset($serverParams['PHP_AUTH_USER'])) {
            $password = $serverParams['PHP_AUTH_PW'] ?? '';
            $headers['Authorization'] = ['Basic ' . \base64_encode($serverParams['PHP_AUTH_USER'] . ':' . $password)];
        }
        return $headers;
    }

    protected static function readVersionFromServerParams(array $serverParams): string
    {
        if (isset($serverParams['SERVER_PROTOCOL'])) {
            /** @psalm-suppress MixedAssignment */
            $protocol = $serverParams['SERVER_PROTOCOL'];
            if ($protocol) {
                return str_replace('HTTP/', '', (string) $protocol);
            }
        }
        return '1.1';
    }

    protected static function readBodyFromInputStream(string $path): StreamInterface
    {
        $resource = fopen($path, 'r+');
        if ($resource === false) {
            error_log('Cannot open stream for reading: ' . $path);
            return Stream::empty();
        }
        return Stream::fromResource($resource);
    }

    protected static function readUploadedFilesFromFileParams(array $files): array
    {
        // @todo finish parsing
        return [];
    }
}
