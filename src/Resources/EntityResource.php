<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Entities\Entity;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\ConditionalResponse;
use Hamlet\Http\Responses\MethodNotAllowedResponse;
use Hamlet\Http\Responses\OKResponse;
use Hamlet\Http\Responses\Response;

class EntityResource implements HttpResource
{
    /**
     * @var array<string>
     */
    protected array $methods;

    public function __construct(protected Entity $entity, string ...$methods)
    {
        $this->methods = $methods ?: ['GET'];
    }

    public function getResponse(Request $request): Response
    {
        if (in_array($request->getMethod(), $this->methods)) {
            return new ConditionalResponse(new OKResponse($this->entity));
        }
        return new MethodNotAllowedResponse(... $this->methods);
    }
}
