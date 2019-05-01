<?php

namespace Hamlet\Http\Applications;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Resources\HttpResource;
use Hamlet\Http\Responses\Response;
use Hamlet\Http\Writers\ResponseWriter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SessionHandlerInterface;

abstract class AbstractApplication
{
    public function run(Request $request): Response
    {
        $resource = $this->findResource($request);
        $response = $resource->getResponse($request);
        return $response;
    }

    abstract protected function findResource(Request $request): HttpResource;

    abstract protected function getCache(Request $request): CacheItemPoolInterface;

    /**
     * @param Request $request
     * @param Response $response
     * @param ResponseWriter $writer
     * @return void
     */
    public function output(Request $request, Response $response, ResponseWriter $writer)
    {
        $cacheProvider = function () use ($request): CacheItemPoolInterface {
            return $this->getCache($request);
        };
        $response->output($request, $cacheProvider, $writer);
    }

    public function sessionHandler(): ?SessionHandlerInterface
    {
        return null;
    }

    public function logger(): LoggerInterface
    {
        return new NullLogger();
    }
}
