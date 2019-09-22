<?php

namespace Hamlet\Http\Entities;

abstract class AbstractJsonEntity extends AbstractEntity
{
    /**
     * @return mixed
     */
    abstract protected function getData();

    public function getMediaType(): string
    {
        return 'application/json;charset=utf-8';
    }
}
