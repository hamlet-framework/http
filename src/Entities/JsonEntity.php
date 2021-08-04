<?php

namespace Hamlet\Http\Entities;

use RuntimeException;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function md5;

class JsonEntity extends AbstractJsonEntity
{

    private ?string $content = null;
    private ?string $key = null;

    public function __construct(private mixed $data) {}

    /**
     * Get the entity key, 304 response needs a proper key value
     */
    public function getKey(): string
    {
        if ($this->key === null) {
            $this->key = md5($this->getContent());
        }
        return $this->key;
    }

    public function getContent(): string
    {
        if ($this->content === null) {
            $content = json_encode($this->data);
            if ($content === false) {
                throw new RuntimeException(json_last_error_msg(), json_last_error());
            }
            $this->content = $content;
        }
        return $this->content;
    }

    protected function getData(): mixed
    {
        return $this->data;
    }
}
