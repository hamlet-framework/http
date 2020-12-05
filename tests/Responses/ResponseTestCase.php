<?php

namespace Hamlet\Http\Responses;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Hamlet\Http\Cache\CacheValue;
use Hamlet\Http\Entities\PlainTextEntity;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Writers\StringResponseWriter;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;

class ResponseTestCase extends TestCase
{
    private $cacheProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheProvider = function () {
            return new ArrayCachePool();
        };
    }

    protected function render(Response $response, Request $request = null): string
    {
        $writer = new StringResponseWriter();
        $response->output($request ?? DefaultRequest::empty(), $this->cacheProvider, $writer);

        return (string) $writer;
    }

    protected function response(string $payload, int $modified)
    {
        $entity = new PlainTextEntity($payload);

        /** @var CacheItemPoolInterface $cache */
        $cache = ($this->cacheProvider)();
        $entity->load($cache);

        try {
            $cacheItem = $cache->getItem($entity->getKey());
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException('Cannot read from cache', 0, $e);
        }
        $cacheItem->set(new CacheValue($entity->getContent(), $modified, $modified));

        return new OKResponse($entity);
    }
}
