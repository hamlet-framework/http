<?php

namespace Hamlet\Http\Writers;

use Psr\Http\Message\ServerRequestInterface;

class DefaultResponseWriter implements ResponseWriter
{
    public function status(int $code, string $line = null)
    {
        if ($line !== null) {
            header($line);
        }
    }

    public function header(string $key, string $value)
    {
        header($key . ': ' . $value);
    }

    public function writeAndEnd(string $payload)
    {
        echo $payload;
        exit;
    }

    public function end()
    {
        exit;
    }

    public function cookie(string $name, string $value, int $expires, string $path, string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httpOnly);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array<string,string> $params
     */
    public function session(ServerRequestInterface $request, array $params)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        foreach ($params as $name => $value) {
            $_SESSION[$name] = $value;
        }
    }
}
