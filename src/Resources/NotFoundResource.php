<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Entities\Entity;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\MethodNotAllowedResponse;
use Hamlet\Http\Responses\NotFoundResponse;
use Hamlet\Http\Responses\Response;

class NotFoundResource implements HttpResource
{
    /**
     * @var Entity|null
     */
    protected $entity;

    public function __construct(Entity $entity = null)
    {
        $this->entity = $entity;
    }

    public function getResponse(Request $request): Response
    {
        if ($request->getMethod() == 'GET') {
            return new NotFoundResponse($this->entity);
        }
        return new MethodNotAllowedResponse('GET');
    }
}
