<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Writers\ResponseWriter;

class ConditionalResponse extends Response
{
    /** @var Response */
    private $response;

    public function __construct(Response $response)
    {
        parent::__construct();
        $this->response = $response;
    }

    /**
     * @param Request $request
     * @param callable():\Psr\Cache\CacheItemPoolInterface $cacheProvider
     * @param ResponseWriter $writer
     */
    public function output(Request $request, callable $cacheProvider, ResponseWriter $writer)
    {
        if ($this->response->entity) {
            $entry = $this->response->entity->load($cacheProvider());
            if ($request->hasHeader('If-Match') && !$request->ifMatch($entry->tag()) ||
                $request->hasHeader('If-Unmodified-Since') && !$request->ifUnmodifiedSince($entry->modified())) {
                $this->withStatusCode(412)->withEmbedEntity(false);
                parent::output($request, $cacheProvider, $writer);
                return;
            }
            if ($request->hasHeader('If-None-Match') && !$request->ifNoneMatch($entry->tag()) ||
                $request->hasHeader('If-Modified-Since') && !$request->ifModifiedSince($entry->modified())) {
                $this->withStatusCode(304)->withEmbedEntity(false);
                parent::output($request, $cacheProvider, $writer);
                return;
            }
        }
        $this->response->output($request, $cacheProvider, $writer);
        return;
    }
}
