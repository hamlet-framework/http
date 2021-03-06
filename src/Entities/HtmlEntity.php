<?php

namespace Hamlet\Http\Entities;

use function md5;

class HtmlEntity extends AbstractEntity
{
    /**
     * @var string
     */
    private $data;

    /**
     * @var string|null
     */
    private $key = null;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function getKey(): string
    {
        if ($this->key === null) {
            $this->key = md5($this->data);
        }
        return $this->key;
    }

    /**
     * @return string|null
     */
    public function getMediaType()
    {
        return "text/html;charset=utf-8";
    }

    public function getContent(): string
    {
        return $this->data;
    }
}
