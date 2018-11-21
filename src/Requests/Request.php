<?php

namespace Hamlet\Http\Requests;

use Psr\Http\Message\ServerRequestInterface;

interface Request extends ServerRequestInterface
{
    public function getPath(): string;

    public function hasQueryParam(string $name): bool;

    /**
     * @param string $name
     * @param string|null $default
     * @return string|array<string>|null
     */
    public function getQueryParam(string $name, string $default = null);

    public function pathMatches(string $path): bool;

    /**
     * @param string $pattern
     * @return string[]|bool
     */
    public function pathMatchesPattern(string $pattern);

    public function pathStartsWith(string $prefix): bool;

    /**
     * @param string $pattern
     * @return string[]|bool
     */
    public function pathStartsWithPattern(string $pattern);

    public function preconditionFulfilled(string $tag, int $lastModified): bool;
}
