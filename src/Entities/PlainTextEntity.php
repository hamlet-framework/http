<?php

namespace Hamlet\Http\Entities;

use function md5;

class PlainTextEntity extends AbstractEntity
{
    private ?string $key = null;

    public function __construct(private string $data) {}

    public function getKey(): string
    {
        if ($this->key === null) {
            $this->key = md5($this->data);
        }
        return $this->key;
    }

    public function getMediaType(): ?string
    {
        return "text/plain;charset=utf-8";
    }

    public function getContent(): string
    {
        return $this->data;
    }
}
