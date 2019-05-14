<?php

namespace Hamlet\Http\Writers;

class StringResponseWriter implements ResponseWriter
{
    /**
     * @var string|null
     */
    private $statusLine;

    /**
     * @var string[]
     * @psalm-var array<string,string>
     */
    private $headers = [];

    /**
     * @var string|null
     */
    private $payload;

    /**
     * @param int $code
     * @param string|null $line
     * @return void
     */
    public function status(int $code, string $line = null)
    {
        $this->statusLine = $line;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function header(string $key, string $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @param string $payload
     * @return void
     */
    public function writeAndEnd(string $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return void
     */
    public function end()
    {
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
