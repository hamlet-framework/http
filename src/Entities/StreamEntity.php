<?php

namespace Hamlet\Http\Entities;

use Psr\Http\Message\StreamInterface;
use function md5;

class StreamEntity extends AbstractEntity
{
    private ?string $content = null;

    public function __construct(private StreamInterface $stream) {}

    public function getContent(): string
    {
        if (!isset($this->content)) {
            $this->stream->rewind();
            $this->content = $this->stream->getContents();
        }
        return $this->content;
    }

    public function getKey(): string
    {
        return md5($this->getContent());
    }

    public function getMediaType(): ?string
    {
        return null;
    }
}
