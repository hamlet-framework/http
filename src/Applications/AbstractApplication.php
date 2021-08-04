<?php

namespace Hamlet\Http\Applications;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Resources\HttpResource;
use Hamlet\Http\Responses\Response;
use Hamlet\Http\Writers\ResponseWriter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class AbstractApplication
{
    public function run(Request $request): Response
    {
        $resource = $this->findResource($request);
        return $resource->getResponse($request);
    }

    abstract protected function findResource(Request $request): HttpResource;

    abstract protected function getCache(Request $request): CacheItemPoolInterface;

    public function output(Request $request, Response $response, ResponseWriter $writer): void
    {
        $cacheProvider = function () use ($request): CacheItemPoolInterface {
            return $this->getCache($request);
        };
        $response->output($request, $cacheProvider, $writer);
    }

    public function logger(): LoggerInterface
    {
        return new NullLogger();
    }
}
