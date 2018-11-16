<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\Response;
use Hamlet\Http\Responses\UnauthorizedResponse;

class UnauthorizedResource implements HttpResource
{
    public function getResponse(Request $request): Response
    {
        return new UnauthorizedResponse();
    }
}
