<?php

namespace Hamlet\Http\Requests;

use Hamlet\Cast\Type;
use function count;
use function explode;
use function strlen;
use function substr;
use function urldecode;

trait RequestTrait
{
    abstract public function getPath(): string;

    abstract public function getQueryParams(): array;

    abstract public function getParsedBody();

    public function hasQueryParam(string $name): bool
    {
        $params = $this->getQueryParams();
        return array_key_exists($name, $params);
    }

    public function hasBodyParam(string $name): bool
    {
        $param = $this->getParsedBody();
        return is_array($param) && array_key_exists($name, $param);
    }

    /**
     * @template T
     * @param string $name
     * @param Type $type
     * @psalm-param Type<T> $type
     * @return mixed
     * @psalm-return T
     */
    public function getQueryParam(string $name, Type $type)
    {
        $params = $this->getQueryParams();
        assert(array_key_exists($name, $params));
        return $type->cast($params[$name]);
    }

    /**
     * @template T
     * @param string $name
     * @param Type $type
     * @psalm-param Type<T> $type
     * @return mixed
     * @psalm-return T
     */
    public function getBodyParam(string $name, Type $type)
    {
        $params = $this->getParsedBody();
        assert(is_array($params) && array_key_exists($name, $params));
        return $type->cast($params[$name]);
    }

    /**
     * Compare path tokens side by side. Returns false if no match, true if match without capture,
     * and array with matched tokens if used with capturing pattern
     *
     * @param string[] $pathTokens
     * @param string[] $patternTokens
     *
     * @return array<string,string>|bool
     */
    protected function matchTokens(array $pathTokens, array $patternTokens)
    {
        $matches = [];
        for ($i = 1; $i < count($patternTokens); $i++) {
            $pathToken = $pathTokens[$i];
            $patternToken = $patternTokens[$i];
            if ($pathToken == '' && $patternToken != '') {
                return false;
            }
            if ($patternToken == '*') {
                continue;
            }
            if (substr($patternToken, 0, 1) == '{') {
                $matches[substr($patternToken, 1, -1)] = urldecode($pathToken);
            } else if (urldecode($pathToken) != $patternToken) {
                return false;
            }
        }
        if (empty($matches)) {
            return true;
        }
        return $matches;
    }

    public function pathMatches(string $path): bool
    {
        return $this->getPath() == $path;
    }

    /**
     * @param string $pattern
     * @return array<string,string>|bool
     */
    public function pathMatchesPattern(string $pattern)
    {
        $path = $this->getPath();
        $pathTokens = explode('/', $path);
        $patternTokens = explode('/', $pattern);
        if (count($pathTokens) != count($patternTokens)) {
            return false;
        }
        return $this->matchTokens($pathTokens, $patternTokens);
    }

    public function pathStartsWith(string $prefix): bool
    {
        $path = $this->getPath();
        return substr($path, 0, strlen($prefix)) == $prefix;
    }

    /**
     * @param string $pattern
     * @return array<string,string>|bool
     */
    public function pathStartsWithPattern(string $pattern)
    {
        $path = $this->getPath();
        $pathTokens = explode('/', $path);
        $patternTokens = explode('/', $pattern);
        return $this->matchTokens($pathTokens, $patternTokens);
    }
}
