<?php

namespace Hamlet\Http\Cache;

use Hamlet\Http\Requests\Validator;

class CacheValue
{
    private string $tag;
    private string $digest;
    private int $length;

    public function __construct(
        private string $content,
        private int $modified,
        private int $expiry
    ) {
        $md5 = md5($content);
        $this->tag      = '"' . $md5 . '"';
        $this->digest   = base64_encode(pack('H*', $md5));
        $this->length   = strlen($content);
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

    public function validator(): Validator
    {
        return new Validator($this->tag, $this->modified);
    }
}
