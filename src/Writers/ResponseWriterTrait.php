<?php

namespace Hamlet\Http\Writers;

trait ResponseWriterTrait
{
    abstract public function header(string $key, string $value): void;

    public function cookie(string $name, string $value, int $expires, string $path, string $domain = '', bool $secure = false, bool $httpOnly = false): void
    {
        $header = urlencode($name) . '=' . urlencode($value) . '; Path=' . $path;
        if ($expires) {
            $header .= '; Expires=' . date('D, d M Y, H:i:s \G\M\T', $expires);
        }
        if (!empty($domain)) {
            $header .= '; Domain=' . urlencode($domain);
        }
        if (!$secure) {
            $header .= '; Secure';
        }
        if ($httpOnly) {
            $header .= '; HttpOnly';
        }
        $this->header('Set-Cookie', $header);
    }
}
