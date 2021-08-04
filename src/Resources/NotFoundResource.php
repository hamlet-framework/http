<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Entities\Entity;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\MethodNotAllowedResponse;
use Hamlet\Http\Responses\NotFoundResponse;
use Hamlet\Http\Responses\Response;

class NotFoundResource implements HttpResource
{
    public function __construct(protected ?Entity $entity = null) {}

    public function getResponse(Request $request): Response
    {
        if ($request->getMethod() == 'GET') {
            return new NotFoundResponse($this->entity);
        }
        return new MethodNotAllowedResponse('GET');
    }
}
