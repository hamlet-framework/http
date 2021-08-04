<?php

namespace Hamlet\Http\Writers;

class StringResponseWriter implements ResponseWriter
{
    private ?string $statusLine = null;

    /**
     * @var array<string,string>
     */
    private array $headers = [];

    private ?string $payload = null;

    public function status(int $code, string $line = null): void
    {
        $this->statusLine = $line;
    }

    public function header(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function writeAndEnd(string $payload): void
    {
        $this->payload = $payload;
    }

    public function end(): void
    {
    }

    public function cookie(string $name, string $value, int $expires, string $path, string $domain = '', bool $secure = false, bool $httpOnly = false): void
    {
        // TODO: Implement cookie() method.
    }

    public function __toString(): string
    {
        $result = '';
        if ($this->statusLine) {
            $result .= $this->statusLine . "\r\n";
        }
        foreach ($this->headers as $key => $value) {
            $result .= $key . ": " . $value . "\r\n";
        }
        $result .= "\r\n";
        if ($this->payload) {
            $result .= $this->payload;
        }
        return $result;
    }
}
