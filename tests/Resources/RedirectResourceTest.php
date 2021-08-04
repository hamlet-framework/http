<?php

namespace Hamlet\Http\Resources;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Hamlet\Http\Entities\JsonEntity;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Writers\StringResponseWriter;
use PHPUnit\Framework\TestCase;

class RedirectResourceTest extends TestCase
{
    public function testResponseRenderedProperly()
    {
        $url = 'https://example.dev';
        $resource = new RedirectResource($url);
        $request = DefaultRequest::empty();

        $response = $resource->getResponse($request);
        $writer = new StringResponseWriter();
        $response->output($request, fn () => new ArrayCachePool, $writer);
        $renderedResponse = (string) $writer;

        $this->assertStringContainsString('HTTP/1.1 307 Temporary Redirect', $renderedResponse);
        $this->assertStringContainsString('Location: https://example.dev', $renderedResponse);
    }

    public function testDefaultMethodMismatchReturns405()
    {
        $url = 'https://example.dev';
        $resource = new RedirectResource($url);
        $request = DefaultRequest::empty()->withMethod('POST');

        $response = $resource->getResponse($request);
        $writer = new StringResponseWriter();
        $response->output($request, fn () => new ArrayCachePool, $writer);
        $renderedResponse = (string) $writer;

        $this->assertStringContainsString('HTTP/1.1 405 Method Not Allowed', $renderedResponse);
        $this->assertStringContainsString('Allow: GET', $renderedResponse);
    }
}
