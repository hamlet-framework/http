<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Requests\Request;
use Hamlet\Http\Writers\ResponseWriter;

class ConditionalResponse extends Response
{
    /**
     * @var Response
     */
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
            $code = $entry->validator()->evaluateCode($request);
            if ($code == 304 || $code == 412 || $code == 501) {
                $this->withStatusCode($code)->withEmbedEntity(false);
                parent::output($request, $cacheProvider, $writer);
                return;
            }
        }
        $this->response->output($request, $cacheProvider, $writer);
        return;
    }
}
