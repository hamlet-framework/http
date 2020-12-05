<?php

namespace Hamlet\Http\Responses;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Hamlet\Http\Entities\PlainTextEntity;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Writers\StringResponseWriter;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @todo take this documentation: https://tools.ietf.org/html/rfc7232
 * @todo add tests that add 60 seconds of grace period
 */
class ConditionalResponseTest extends TestCase
{
    /** @var ConditionalResponse */
    private $response;

    /** @var callable */
    private $cacheProvider;

    protected function setUp(): void
    {
        $this->response = new ConditionalResponse(new OKResponse(new PlainTextEntity('content')));
        $this->cacheProvider = $this->cacheProvider = function () {
            return new ArrayCachePool();
        };
    }

    public function testIfRangeRequestsReturnNotImplemented()
    {
        $request = DefaultRequest::empty()
            ->withHeader('If-Range', '"1"')
            ->withHeader('Range', '100-999');

        $writer = new StringResponseWriter();
        $this->response->output($request, $this->cacheProvider, $writer);

        Assert::assertStringStartsWith("HTTP/1.1 501 Not Implemented\r\n", (string)$writer);
    }

    public function testIfMatchReturns200OnMatchingSingleTag()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Match', '"9a0364b9e99bb480dd25e1f0284c8555"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfMatchReturns200OnMatchWithMultipleTags()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Match', '"a", "b", "9a0364b9e99bb480dd25e1f0284c8555"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfMatchReturns200OnWildcard()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Match', '"a", *, "c"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfMatchReturns412OnNonMatchingSingleTag()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Match', '"xx"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", (string)$writer);
        }
    }

    public function testIfMatchReturns412OnNonMatchingMultipleTags()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Match', '"a", "b", "c"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", (string)$writer);
        }
    }

    public function testIfNoneMatchReturns200OnNonMatchingSingleTag()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-None-Match', '"a"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfNoneMatchReturns200OnNonMatchingMultipleTag()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-None-Match', '"a", "b", "c"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testPutIfNoneMatchReturns412OnMatchingSingleTag()
    {
        $request = DefaultRequest::empty()
            ->withMethod('PUT')
            ->withHeader('If-None-Match', '"9a0364b9e99bb480dd25e1f0284c8555"');

        $writer = new StringResponseWriter();
        $this->response->output($request, $this->cacheProvider, $writer);

        Assert::assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", (string)$writer);
    }

    public function testPutIfNoneMatchReturns412OnMatchInMultipleTags()
    {
        $request = DefaultRequest::empty()
            ->withMethod('PUT')
            ->withHeader('If-None-Match', '"a", "b", "c", "9a0364b9e99bb480dd25e1f0284c8555"');

        $writer = new StringResponseWriter();
        $this->response->output($request, $this->cacheProvider, $writer);

        Assert::assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", (string)$writer);
    }

    public function testGetOrHeadIfNoneMatchReturns304OnMatchingSingleTag()
    {
        foreach (['GET', 'HEAD'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-None-Match', '"9a0364b9e99bb480dd25e1f0284c8555"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 304 Not Modified\r\n", (string)$writer);
        }
    }

    public function testGetOrHeadIfNoneMatchReturns304OnMatchingInMultipleTags()
    {
        foreach (['GET', 'HEAD'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-None-Match', '"a", "b", "c", "9a0364b9e99bb480dd25e1f0284c8555"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 304 Not Modified\r\n", (string)$writer);
        }
    }

    public function testIfModifiedSinceIgnoredOnInvalidDateFormat()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Modified-Since', 'aa-aa-aa');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfModifiedSinceIgnoredOnNonGetOrHead()
    {
        foreach (['PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Modified-Since', date(DATE_RFC7231, time() + 100));

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfModifiedSinceIgnoredWhenInNoneMatchIsPresent()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Modified-Since', date(DATE_RFC7231, time() + 100))
                ->withHeader('If-None-Match', '"a"');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfModifiedSinceReturns304IfNoModificationSinceOnGetOrHead()
    {
        foreach (['GET', 'HEAD'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Modified-Since', date(DATE_RFC7231, time() + 100));

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 304 Not Modified\r\n", (string)$writer);
        }
    }

    public function testIfModifiedSinceReturns300WhenModified()
    {
        foreach (['GET', 'HEAD'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Modified-Since', date(DATE_RFC7231, time() - 100));

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfUnmodifiedSinceIgnoredOnInvalidDateFormat()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Unmodified-Since', 'aa-aa-aa');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfUnmodifiedSinceIgnoredWhenIfMatchIsPresent()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Unmodified-Since', date(DATE_RFC7231, time() - 100))
                ->withHeader('If-Match', '*');

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfUnmodifiedSinceReturns200WhenNotModified()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Unmodified-Since', date(DATE_RFC7231, time() + 100));

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", (string)$writer);
        }
    }

    public function testIfUnmodifiedSinceReturns412WhenModified()
    {
        foreach (['GET', 'HEAD', 'PUT', 'POST', 'DELETE'] as $method) {
            $request = DefaultRequest::empty()
                ->withMethod($method)
                ->withHeader('If-Unmodified-Since', date(DATE_RFC7231, time() - 100));

            $writer = new StringResponseWriter();
            $this->response->output($request, $this->cacheProvider, $writer);

            Assert::assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", (string)$writer);
        }
    }
}
