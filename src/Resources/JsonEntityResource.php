<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Entities\JsonEntity;

class JsonEntityResource extends EntityResource
{
    /** @noinspection PhpDocSignatureInspection */
    /**
     * JsonEntityResource constructor.
     * @param mixed $value
     * @param string ...$methods
     */
    public function __construct($value, string ... $methods)
    {
        parent::__construct(new JsonEntity($value), ... $methods);
    }
}
