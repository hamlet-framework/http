<?php

namespace Hamlet\Http\Requests;

use Hamlet\Http\Entities\Entity;
use Hamlet\Http\Message\ServerRequest;
use Hamlet\Http\Message\Stream;
use Hamlet\Http\Message\Uri;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use SessionHandlerInterface;

class Request extends ServerRequest
{
    protected const HEADER_ALIASES = [
        'CONTENT_TYPE'                => 'Content-Type',
        'CONTENT_LENGTH'              => 'Content-Length',
        'CONTENT_MD5'                 => 'Content-MD5',
        'REDIRECT_HTTP_AUTHORIZATION' => 'Authorization',
        'PHP_AUTH_DIGEST'             => 'Authorization',
        'HTTP_HOST'                   => 'Host',
        'HTTP_CONNECTION'             => 'Connection',
        'HTTP_CACHE_CONTROL'          => 'Cache-Control',
        'HTTP_UPGRADE_INSECURE_REQUESTS' => 'Upgrade-Insecure-Requests',
        'HTTP_USER_AGENT'             => 'User-Agent',
        'HTTP_DNT'                    => 'DNT',
        'HTTP_ACCEPT'                 => 'Accept',
        'HTTP_ACCEPT_ENCODING'        => 'Accept-Encoding',
        'HTTP_ACCEPT_LANGUAGE'        => 'Accept-Language',
        'HTTP_COOKIE'                 => 'Cookie'
    ];

    public static function fromSuperGlobals(SessionHandlerInterface $sessionHandler = null): self
    {
        $request = new static;

        $request->properties['method']          = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $request->properties['serverParams']    = &$_SERVER;
        $request->properties['cookieParams']    = &$_COOKIE;
        $request->properties['queryParams']     = &$_GET;
        $request->properties['parsedBody']      = &$_POST;
        $request->properties['path']            = strtok((string) $_SERVER['REQUEST_URI'], '?') ?: null;

        $request->generators['protocolVersion'] = [[&$request, 'readProtocolVersion'], &$_SERVER];
        $request->generators['body']            = [[&$request, 'readBodyFromInputStream']];
        $request->generators['headers']         = [[&$request, 'readHeadersFromServerParams'], &$_SERVER];
        $request->generators['uri']             = [[&$request, 'readUriFromServerParams'], &$_SERVER];
        $request->generators['sessionParams']   = [[&$request, 'readSessionParams'], &$sessionHandler];
        $request->generators['uploadedFiles']   = [[&$request, 'readUploadedFiles'], &$_FILES];

        return $request;
    }

    public function getPath(): ?string
    {
        if (\array_key_exists('path', $this->properties)) {
            return $this->properties['path'];
        }

        /** @var UriInterface|null $uri */
        $uri = $this->fetch('uri');
        return $this->properties['path'] = $uri ? $uri->getPath() : null;
    }

    public function hasQueryParam(string $name): bool
    {
        return isset($this->getQueryParams()[$name]);
    }

    /**
     * @param string $name
     * @param string|null $default
     * @return string|array|null
     */
    public function getQueryParam(string $name, string $default = null)
    {
        return $this->getQueryParams()[$name] ?? $default;
    }

    public function sessionStarted(): bool
    {
        return $this->getSessionParams() !== null;
    }

    /**
     * @return array|null
     */
    public function getSessionParams()
    {
        return $this->fetch('sessionParams');
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getSessionParam(string $name, $default = null)
    {
        return $this->getSessionParams()[$name] ?? $default;
    }

    public function hasSessionParam(string $name): bool
    {
        return isset($this->getSessionParams()[$name]);
    }

    /**
     * Compare path tokens side by side. Returns false if no match, true if match without capture,
     * and array with matched tokens if used with capturing pattern
     *
     * @param string[] $pathTokens
     * @param string[] $patternTokens
     *
     * @return array<string,string>|bool
     */
    protected function matchTokens(array $pathTokens, array $patternTokens)
    {
        $matches = [];
        for ($i = 1; $i < \count($patternTokens); $i++) {
            $pathToken = $pathTokens[$i];
            $patternToken = $patternTokens[$i];
            if ($pathToken == '' && $patternToken != '') {
                return false;
            }
            if ($patternToken == '*') {
                continue;
            }
            if (\substr($patternToken, 0, 1) == '{') {
                $matches[\substr($patternToken, 1, -1)] = \urldecode($pathToken);
            } else if (\urldecode($pathToken) != $patternToken) {
                return false;
            }
        }
        if (empty($matches)) {
            return true;
        }
        return $matches;
    }

    public function pathMatches(string $path): bool
    {
        return $this->getPath() == $path;
    }

    /**
     * @param string $pattern
     * @return string[]|bool
     */
    public function pathMatchesPattern(string $pattern)
    {
        $path = $this->getPath();
        if ($path === null) {
            return false;
        }
        $pathTokens = \explode('/', $path);
        $patternTokens = \explode('/', $pattern);
        if (\count($pathTokens) != \count($patternTokens)) {
            return false;
        }
        return $this->matchTokens($pathTokens, $patternTokens);
    }

    public function pathStartsWith(string $prefix): bool
    {
        $path = $this->getPath();
        if ($path === null) {
            return false;
        }
        return \substr($path, 0, \strlen($prefix)) == $prefix;
    }

    /**
     * @param string $pattern
     * @return string[]|bool
     */
    public function pathStartsWithPattern(string $pattern)
    {
        $path = $this->getPath();
        if ($path === null) {
            return false;
        }
        $pathTokens = explode('/', $path);
        $patternTokens = explode('/', $pattern);
        return $this->matchTokens($pathTokens, $patternTokens);
    }

    /**
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Match
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-None-Match
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Modified-Since
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Unmodified-Since
     * @param Entity $entity
     * @param callable():\Psr\Cache\CacheItemPoolInterface $cacheProvider
     * @return bool
     */
    public function preconditionFulfilled(Entity $entity, callable $cacheProvider): bool
    {
        $matchHeaders           = $this->getHeader('If-Match');
        $noneMatchHeaders       = $this->getHeader('If-None-Match');
        $modifiedSinceHeaders   = $this->getHeader('If-Modified-Since');
        $unmodifiedSinceHeaders = $this->getHeader('If-Unmodified-Since');

        if (empty($matchHeaders) && empty($noneMatchHeaders) && empty($modifiedSinceHeaders) && empty($unmodifiedSinceHeaders)) {
            return true;
        }

        $cache = $cacheProvider();

        $cacheEntry   = $entity->load($cache);
        $tag          = $cacheEntry->tag();
        $lastModified = $cacheEntry->modified();

        if (!empty($matchHeaders)) {
            $matchHeader = $matchHeaders[0];
            if ($matchHeader == '*') {
                return true;
            }
            foreach (explode(',', $matchHeader) as $matchTag) {
                if ($tag == trim($matchTag)) {
                    return true;
                }
            }
        }

        if (!empty($noneMatchHeaders)) {
            $noneMatchHeader = $noneMatchHeaders[0];
            if ($noneMatchHeader == '*') {
                return true;
            }
            $matchFound = false;
            foreach (explode(',', $noneMatchHeader) as $noneMatchTag) {
                if ($tag == $noneMatchTag) {
                    $matchFound = true;
                    break;
                }
            }
            if (!$matchFound) {
                return true;
            }
        }

        if (!empty($modifiedSinceHeaders)) {
            $modifiedSinceHeader = $modifiedSinceHeaders[0];
            if ($lastModified > strtotime($modifiedSinceHeader)) {
                return true;
            }
        }

        if (!empty($unmodifiedSinceHeaders)) {
            $unmodifiedSinceHeader = $unmodifiedSinceHeaders[0];
            if ($lastModified < strtotime($unmodifiedSinceHeader)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string,string> $serverParams
     * @return array<string,array<int,string>>
     */
    protected function readHeadersFromServerParams(array $serverParams): array
    {
        if (\function_exists('getallheaders')) {
            $headers = \getallheaders();
            // @todo make sure headers have 'Host' at the top
            // @todo make sure header values are wrapped into arrays
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
                $headers[self::HEADER_ALIASES[$name]][] = $value;
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

    /**
     * Get a Uri populated with values from server params.
     * @param array<string,string> $serverParams
     * @return UriInterface
     */
    protected function readUriFromServerParams(array $serverParams): UriInterface
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

    protected function readVersionFromProtocol(?string $protocol): string
    {
        return $protocol !== null ? str_replace('HTTP/', '', $protocol) : '1.1';
    }

    protected function readSessionParams(?SessionHandlerInterface $sessionHandler): ?array
    {
        if (session_status() == PHP_SESSION_NONE) {
            if ($sessionHandler !== null) {
                session_set_save_handler($sessionHandler);
            }
            session_start();
            return $_SESSION;
        }
        return null;
    }

    protected function readBodyFromInputStream(): StreamInterface
    {
        $resource = fopen('php://input', 'r+');
        return Stream::fromResource($resource);
    }
}
