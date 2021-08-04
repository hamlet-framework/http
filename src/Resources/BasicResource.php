<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\Response;

class BasicResource implements HttpResource
{
    public function __construct(protected Response $response) {}

    public function getResponse(Request $request): Response
    {
        return $this->response;
    }
}
