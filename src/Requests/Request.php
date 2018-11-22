<?php

namespace Hamlet\Http\Requests;

use Psr\Http\Message\ServerRequestInterface;

interface Request extends ServerRequestInterface
{
    public function getPath(): string;

    public function hasParameter(string $name): bool;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function parameter(string $name, $default = null);

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
