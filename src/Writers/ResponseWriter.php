<?php

namespace Hamlet\Http\Writers;

interface ResponseWriter
{
    /**
     * @param int $code
     * @param string|null $line
     * @return void
     */
    public function status(int $code, string $line = null);

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function header(string $key, string $value);

    /**
     * @param string $payload
     * @return void
     */
    public function writeAndEnd(string $payload);

    /**
     * @return void
     */
    public function end();

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
    public function cookie(string $name, string $value, int $expires, string $path, string $domain = '', bool $secure = false, bool $httpOnly = false);
}
