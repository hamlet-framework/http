<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Entities\JsonEntity;

class JsonEntityResource extends EntityResource
{
    public function __construct(mixed $value, string ...$methods)
    {
        parent::__construct(new JsonEntity($value), ...$methods);
    }
}
