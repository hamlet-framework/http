<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\Entity;
use Hamlet\Http\Requests\Request;
use Hamlet\Http\Writers\ResponseWriter;

class OKOrNotModifiedResponse extends Response
{

    public function __construct(Entity $entity)
    {
        parent::__construct();
        $this->withEntity($entity);
    }

    /**
     * @param Request $request
     * @param callable():\Psr\Cache\CacheItemPoolInterface $cacheProvider
     * @param ResponseWriter $writer
     */
    public function output(Request $request, callable $cacheProvider, ResponseWriter $writer)
    {
        if ($this->entity) {
            $entry = $this->entity->load($cacheProvider());
            if ($request->preconditionFulfilled($entry->tag(), $entry->modified())) {
                $this->withStatusCode(200)->withEmbedEntity(true);
            } else {
                $this->withStatusCode(304)->withEmbedEntity(false);
            }
        } else {
            $this->withStatusCode(304)->withEmbedEntity(false);
        }
        parent::output($request, $cacheProvider, $writer);
    }
}
