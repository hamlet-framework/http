<?php

namespace Hamlet\Http\Requests;

use Hamlet\Cast\Type;
use Psr\Http\Message\ServerRequestInterface;

interface Request extends ServerRequestInterface
{
    public function getPath(): string;

    /**
     * @param string $name
     * @return bool
     * @deprecated
     */
    public function hasParameter(string $name): bool;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @deprecated
     */
    public function parameter(string $name, $default = null);

    public function hasQueryParam(string $name): bool;

    public function hasBodyParam(string $name): bool;

    /**
     * @template T
     * @param string $name
     * @param Type $type
     * @psalm-param Type<T> $type
     * @return mixed
     * @psalm-return T
     */
    public function getQueryParam(string $name, Type $type);

    /**
     * @template T
     * @param string $name
     * @param Type $type
     * @psalm-param Type<T> $type
     * @return mixed
     * @psalm-return T
     */
    public function getBodyParam(string $name, Type $type);

    public function pathMatches(string $path): bool;

    /**
     * @param string $pattern
     * @return array<string,string>|bool
     */
    public function pathMatchesPattern(string $pattern);

    public function pathStartsWith(string $prefix): bool;

    /**
     * @param string $pattern
     * @return array<string,string>|bool
     */
    public function pathStartsWithPattern(string $pattern);

    public function preconditionFulfilled(string $tag, int $lastModified): bool;
}
