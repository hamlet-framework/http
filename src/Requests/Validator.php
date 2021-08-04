<?php

namespace Hamlet\Http\Requests;

use RuntimeException;

/**
 * @see https://tools.ietf.org/html/rfc7232#section-2
 */
class Validator
{
    private string $entityTag;

    private int $lastModifiedTime;

    public static function strongMatch(string $entityTag1, string $entityTag2): bool
    {
        if ($entityTag1 == '*' || $entityTag2 == '*') {
            return true;
        } elseif (preg_match('|^W/|', $entityTag1) || preg_match('|^W/|', $entityTag2)) {
            return false;
        } else {
            return $entityTag1 == $entityTag2;
        }
    }

    public static function weakMatch(string $entityTag1, string $entityTag2): bool
    {
        if ($entityTag1 == '*' || $entityTag2 == '*') {
            return true;
        } else {
            return preg_replace('|^W/|', '', $entityTag1) == preg_replace('|^W/|', '', $entityTag2);
        }
    }

    public function __construct(string $entityTag, int $lastModifiedTime)
    {
        $this->entityTag = $entityTag;
        $this->lastModifiedTime = $lastModifiedTime;
    }

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.1
     */
    public function matchConditionSatisfied(Request $request): bool
    {
        if (!$request->hasHeader('If-Match')) {
            throw new RuntimeException('If-Match header not set');
        }
        foreach ($request->getHeader('If-Match') as $value) {
            $items = explode(',', $value);
            foreach ($items as $item) {
                $cleanItem = trim($item);
                if (self::strongMatch($cleanItem, $this->entityTag)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @see https://tools.ietf.org/html/rfc7232#section-3.2
     */
    public function noneMatchConditionSatisfied(Request $request): bool
    {
        if (!$request->hasHeader('If-None-Match')) {
            throw new RuntimeException('If-None-Match header not set');
        }
        foreach ($request->getHeader('If-None-Match') as $value) {
            $items = explode(',', $value);
            foreach ($items as $item) {
                $cleanItem = trim($item);
                if (self::weakMatch($cleanItem, $this->entityTag)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function modifiedSinceConditionSatisfied(Request $request): bool
    {
        if (!$request->hasHeader('If-Modified-Since')) {
            throw new RuntimeException('If-Modified-Since header not set');
        }
        foreach ($request->getHeader('If-Modified-Since') as $value) {
            $timestamp = strtotime($value);
            if (!$timestamp || $this->lastModifiedTime + 60 > $timestamp) {
                return true;
            }
        }
        return false;
    }

    public function unmodifiedSinceConditionSatisfied(Request $request): bool
    {
        if (!$request->hasHeader('If-Unmodified-Since')) {
            throw new RuntimeException('If-Unmodified-Since header not set');
        }
        foreach ($request->getHeader('If-Unmodified-Since') as $value) {
            $timestamp = strtotime($value);
            if (!$timestamp || $this->lastModifiedTime + 60 < $timestamp) {
                return true;
            }
        }
        return false;
    }

    /**
     * Implementation of precedence algorithm https://tools.ietf.org/html/rfc7232#section-6
     */
    public function evaluateCode(Request $request): ?int
    {
        if ($request->hasHeader('If-Match')) {
            if (!$this->matchConditionSatisfied($request)) {
                return 412;
            }
        } elseif ($request->hasHeader('If-Unmodified-Since')) {
            if (!$this->unmodifiedSinceConditionSatisfied($request)) {
                return 412;
            }
        }

        if ($request->hasHeader('If-None-Match')) {
            if (!$this->noneMatchConditionSatisfied($request)) {
                if ($request->getMethod() == 'GET' || $request->getMethod() == 'HEAD') {
                    return 304;
                } else {
                    return 412;
                }
            }
        } elseif ($request->hasHeader('If-Modified-Since')) {
            if (!$this->modifiedSinceConditionSatisfied($request)) {
                if ($request->getMethod() == 'GET' || $request->getMethod() == 'HEAD') {
                    return 304;
                }
            }
        }

        if ($request->getMethod() == 'GET') {
            if ($request->hasHeader('Range') && $request->hasHeader('If-Range')) {
                return 501;
            }
        }
        return null;
    }
}
