<?php

namespace Hamlet\Http\Requests;

use Psr\Http\Message\ServerRequestInterface;

interface Request extends ServerRequestInterface
{
    public function getPath(): ?string;

    public function hasQueryParam(string $name): bool;

    /**
     * @param string $name
     * @param string|null $default
     * @return string|array|null
     */
    public function getQueryParam(string $name, string $default = null);
}
