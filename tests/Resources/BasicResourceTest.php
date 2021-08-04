<?php

namespace Hamlet\Http\Resources;

use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Responses\OKResponse;
use PHPUnit\Framework\TestCase;

class BasicResourceTest extends TestCase
{
    public function testResponseChanneledUnchanged()
    {
        $response = new OKResponse();
        $resource = new BasicResource($response);
        $request = DefaultRequest::empty();

        $this->assertSame($response, $resource->getResponse($request));
    }
}
