<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\Entity;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Writers\ResponseWriter;

/**
 * Basic OK response with absolute minimum of headers
 */
class SimpleOKResponse extends Response
{
    public function __construct(Entity $entity)
    {
        parent::__construct(200);
        $this->withEntity($entity);
    }

    /**
     * @param Request $request
     * @param callable $cacheProvider
     * @psalm-param callable():\Psr\Cache\CacheItemPoolInterface $cacheProvider
     * @param ResponseWriter $writer
     */
    public function output(Request $request, callable $cacheProvider, ResponseWriter $writer)
    {
        $writer->status($this->statusCode, $this->getStatusLine());
        assert($this->entity !== null);
        $content = $this->entity->getContent();
        $writer->header('Content-Length', (string) strlen($content));
        $mediaType = $this->entity->getMediaType();
        if ($mediaType) {
            $writer->header('Content-Type', $mediaType);
        }
        $writer->writeAndEnd($content);
    }
}
