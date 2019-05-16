<?php

namespace Hamlet\Http\Responses;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Writers\StringResponseWriter;
use PHPUnit\Framework\TestCase;

class ResponseTestCase extends TestCase
{
    protected function render(Response $response, Request $request = null): string
    {
        $writer = new StringResponseWriter();
        $cacheProvider = function () {
            return new ArrayCachePool();
        };
        $response->output($request ?? DefaultRequest::empty(), $cacheProvider, $writer);

        return (string) $writer;
    }
}
