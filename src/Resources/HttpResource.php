<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Responses\Response;

interface HttpResource
{
    public function getResponse(Request $request): Response;
}
