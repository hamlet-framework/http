<?php

namespace Hamlet\Http\Cache;

class CacheValue
{
    /** @var string */
    private $content;

    /** @var string */
    private $tag;

    /** @var string */
    private $digest;

    /** @var int */
    private $length;

    /** @var int */
    private $modified;

    /** @var int */
    private $expiry;

    public function __construct(string $content, int $modified, int $expiry)
    {
        $md5 = md5($content);

        $this->content  = $content;
        $this->tag      = '"' . $md5 . '"';
        $this->digest   = base64_encode(pack('H*', $md5));
        $this->length   = strlen($content);
        $this->modified = $modified;
        $this->expiry   = $expiry;
    }

    public function extendExpiry(int $expires): CacheValue
    {
        return new CacheValue($this->content, $this->modified, $expires);
    }

    public function content(): string
    {
        return $this->content;
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function digest(): string
    {
        return $this->digest;
    }

    public function length(): int
    {
        return $this->length;
    }

    public function modified(): int
    {
        return $this->modified;
    }

    public function expiry(): int
    {
        return $this->expiry;
    }
}
