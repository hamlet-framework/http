<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\MethodNotAllowedResponse;
use Hamlet\Http\Responses\Response;
use Hamlet\Http\Responses\TemporaryRedirectResponse;

class RedirectResource implements HttpResource
{
    public function __construct(protected string $url) {}

    public function getResponse(Request $request): Response
    {
        if ($request->getMethod() == 'GET') {
            return new TemporaryRedirectResponse($this->url);
        }
        return new MethodNotAllowedResponse('GET');
    }
}
