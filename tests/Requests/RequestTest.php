<?php

namespace Hamlet\Http\Requests;

use Hamlet\Http\Message\Spec\Traits\DataProviderTrait;
use Hamlet\Http\Message\Spec\Traits\MessageTestTrait;
use Hamlet\Http\Message\Spec\Traits\RequestTestTrait;
use Hamlet\Http\Message\Spec\Traits\ServerRequestTestTrait;
use Hamlet\Http\Message\Stream;
use Hamlet\Http\Message\Uri;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class RequestTest extends TestCase
{
    use DataProviderTrait;
    use MessageTestTrait;
    use RequestTestTrait;
    use ServerRequestTestTrait;

    protected function serverRequest(): ServerRequestInterface
    {
        return DefaultRequest::empty();
    }

    protected function message(): MessageInterface
    {
        return $this->serverRequest();
    }

    protected function request(): RequestInterface
    {
        return $this->serverRequest();
    }

    protected function stream(): StreamInterface
    {
        return Stream::empty();
    }

    protected function uri(string $value): UriInterface
    {
        return Uri::parse($value);
    }

    public function test_path_changes_after_uri_set()
    {
        $request = DefaultRequest::empty()->withUri(Uri::parse('http://google.com/test?x=2'));

        Assert::assertSame('/test', $request->getPath());
    }
}
