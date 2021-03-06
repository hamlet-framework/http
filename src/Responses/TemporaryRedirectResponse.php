<?php

namespace Hamlet\Http\Responses;

/**
 * In this case, the request should be repeated with another URI; however, future requests should still use the
 * original URI. In contrast to how 302 was historically implemented, the request method is not allowed to be
 * changed when reissuing the original request. For instance, a POST request should be repeated using another POST
 * request.
 */
class TemporaryRedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct(307);
        $this->withHeader('Cache-Control', 'private');
        $this->withHeader('Location', $url);
    }
}
