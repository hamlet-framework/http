<?php

namespace Hamlet\Http\Resources;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Writers\StringResponseWriter;
use PHPUnit\Framework\TestCase;

class JsonEntityResourceTest extends TestCase
{
    public function testResponseRenderedProperly()
    {
        $data = ['message' => 'Good bye!'];
        $resource = new JsonEntityResource($data);
        $request = DefaultRequest::empty();

        $response = $resource->getResponse($request);
        $writer = new StringResponseWriter();
        $response->output($request, fn () => new ArrayCachePool, $writer);
        $renderedResponse = (string) $writer;

        $this->assertStringContainsString('HTTP/1.1 200 OK', $renderedResponse);
        $this->assertStringContainsString('Content-Type: application/json;charset=utf-8', $renderedResponse);
        $this->assertStringContainsString('{"message":"Good bye!"}', $renderedResponse);
    }

    public function testDefaultMethodMismatchReturns405()
    {
        $data = ['message' => 'Good bye!'];
        $resource = new JsonEntityResource($data, 'GET', 'PUT');
        $request = DefaultRequest::empty()->withMethod('POST');

        $response = $resource->getResponse($request);
        $writer = new StringResponseWriter();
        $response->output($request, fn () => new ArrayCachePool, $writer);
        $renderedResponse = (string) $writer;

        $this->assertStringContainsString('HTTP/1.1 405 Method Not Allowed', $renderedResponse);
        $this->assertStringContainsString('Allow: GET, PUT', $renderedResponse);
    }
}
