<?php

namespace Hamlet\Http\Requests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use function array_keys;
use function array_merge;
use function array_reduce;
use function arsort;
use function explode;

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
        return self::parseAcceptLanguageHeader($request->getHeader('Accept-Language'));
    }

    /**
     * @param ServerRequestInterface $request
     * @return string|null
     */
    public static function getRemoteIp(ServerRequestInterface $request)
    {
        if ($request->hasHeader('X-Forwarded-For')) {
            $header = $request->getHeader('X-Forwarded-For');
            if (isset($header[0])) {
                return $header[0];
            }
        }
        $serverParams = $request->getServerParams();
        if (isset($serverParams['REMOTE_ADDR'])) {
            return (string) $serverParams['REMOTE_ADDR'];
        }
        return null;
    }

    /**
     * https://stackoverflow.com/a/48300605/1646086
     * @param array<string> $headers
     * @return array<int,string>
     */
    public static function parseAcceptLanguageHeader(array $headers)
    {
        $weights = [];
        $reducer =
            /**
             * @param array<string,float> $acc
             * @param string $element
             * @return array<string,float>
             * @psalm-suppress TypeDoesNotContainType
             */
            function (array $acc, string $element): array {
                list($l, $q) = array_merge(explode(';q=', $element), ['1']);
                if ($l == '*' || preg_match('#^[A-Za-z]{2,4}([_-]([A-Za-z]{4}|[0-9]{3}))?([_-]([A-Za-z]{2}|[0-9]{3}))?$#', $l)) {
                    $acc[trim($l)] = (float) $q;
                }
                return $acc;
            };

        foreach ($headers as $header) {
            $tokens = explode(',', preg_replace('|\s+|', '', $header));
            /** @var array<string,float> $weights */
            $weights = array_reduce($tokens, $reducer, $weights);
        };
        arsort($weights);
        return array_keys($weights);
    }
}
