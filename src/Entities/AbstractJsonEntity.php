<?php

namespace Hamlet\Http\Entities;

abstract class AbstractJsonEntity extends AbstractEntity
{
    abstract protected function getData(): mixed;

    public function getMediaType(): string
    {
        return 'application/json;charset=utf-8';
    }
}
