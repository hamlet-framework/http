<?php

namespace Hamlet\Http\Writers;

interface ResponseWriter
{
    public function status(int $code, string $line = null): void;

    public function header(string $key, string $value): void;

    public function writeAndEnd(string $payload): void;

    public function end(): void;

    public function cookie(string $name, string $value, int $expires, string $path, string $domain = '', bool $secure = false, bool $httpOnly = false): void;
}
