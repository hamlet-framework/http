<?php

namespace Hamlet\Http\Requests;

use Hamlet\Cast\Type;
use Psr\Http\Message\ServerRequestInterface;

interface Request extends ServerRequestInterface
{
    public function getPath(): string;

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
}
