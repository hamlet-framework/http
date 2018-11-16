<?php

namespace Hamlet\Http\Requests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestUtils
{
    private function __construct()
    {
    }

    /**
     * @param RequestInterface $request
     * @return string[]
     */
    public static function getLanguageCodes(RequestInterface $request): array
    {
        return self::parseHeader($request->getHeader('Accept-Language'));
    }

    /**
     * @param ServerRequestInterface $request
     * @return string|null
     */
    public static function getRemoteIp(ServerRequestInterface $request)
    {
        if ($request->hasHeader('X-Forwarded-For')) {
            return $request->getHeader('X-Forwarded-For')[0];
        }
        $serverParams = $request->getServerParams();
        if (isset($serverParams['REMOTE_ADDR'])) {
            return (string) $serverParams['REMOTE_ADDR'];
        }
        return null;
    }

    /**
     * @param array<string> $headers
     * @return array<int,string>
     */
    private static function parseHeader(array $headers)
    {
        $weights = [];
        $reducer =
            /**
             * @param array<string,float> $acc
             * @param string $element
             * @return array<string,float>
             */
            function (array $acc, string $element): array {
                list($l, $q) = \array_merge(\explode(';q=', $element), ['1']);
                $acc[trim($l)] = (float) $q;
                return $acc;
            };

        foreach ($headers as $header) {
            $tokens = \explode(',', $header);
            /** @var array<string,float> $weights */
            $weights = \array_reduce($tokens, $reducer, $weights);
        };
        \arsort($weights);
        return \array_keys($weights);
    }
}
