<?php

namespace Hamlet\Http\Resources;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Responses\OKResponse;
use Hamlet\Http\Writers\StringResponseWriter;
use PHPUnit\Framework\TestCase;

class UnauthorizedResourceTest extends TestCase
{
    public function testResponseRenderedProperly()
    {
        $resource = new UnauthorizedResource();
        $request = DefaultRequest::empty();

        $response = $resource->getResponse($request);
        $writer = new StringResponseWriter();
        $response->output($request, fn () => new ArrayCachePool, $writer);
        $renderedResponse = (string) $writer;

        $this->assertStringContainsString('HTTP/1.1 401 Unauthorized', $renderedResponse);
    }
}
