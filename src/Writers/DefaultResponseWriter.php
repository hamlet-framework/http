<?php

namespace Hamlet\Http\Writers;

class DefaultResponseWriter implements ResponseWriter
{
    /**
     * @param int $code
     * @param string|null $line
     * @return void
     */
    public function status(int $code, string $line = null)
    {
        if ($line !== null) {
            header($line);
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function header(string $key, string $value)
    {
        header($key . ': ' . $value);
    }

    /**
     * @param string $payload
     * @return void
     */
    public function writeAndEnd(string $payload)
    {
        echo $payload;
        exit;
    }

    /**
     * @return void
     */
    public function end()
    {
        exit;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return void
     */
    public function cookie(string $name, string $value, int $expires, string $path, string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httpOnly);
    }
}
