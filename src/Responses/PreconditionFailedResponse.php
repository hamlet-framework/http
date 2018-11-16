<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\Entity;

class PreconditionFailedResponse extends Response
{
    public function __construct(Entity $entity = null)
    {
        parent::__construct(412);
        if ($entity) {
            $this->withEntity($entity);
        }
    }
}
