<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Entities\Entity;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\Response;
use Hamlet\Http\Responses\MethodNotAllowedResponse;
use Hamlet\Http\Responses\ConditionalResponse;

class EntityResource implements HttpResource
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var string[]
     */
    protected $methods;

    public function __construct(Entity $entity, string ... $methods)
    {
        $this->entity  = $entity;
        $this->methods = $methods ?: ['GET'];
    }

    public function getResponse(Request $request): Response
    {
        if (in_array($request->getMethod(), $this->methods)) {
            $response = new ConditionalResponse($this->entity);
            return $response;
        }
        return new MethodNotAllowedResponse(... $this->methods);
    }
}
