<?php

namespace Hamlet\Http\Entities;

class EntityLocationTuple
{
    public function __construct(
        protected string $location,
        protected Entity $entity
    ) {}

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
