<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\Response;

class BasicResource implements HttpResource
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse(Request $request): Response
    {
        return $this->response;
    }
}
