<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\PlainTextEntity;
use Hamlet\Http\Requests\DefaultRequest;
use PHPUnit\Framework\Assert;

class ConditionalResponseTest extends ResponseTestCase
{
    public function testNoConditionSpecified()
    {
        $response = new ConditionalResponse(new OKResponse(new PlainTextEntity('message')));

        $request = DefaultRequest::empty();
        $payload = $this->render($response, $request);
        Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", $payload);
    }

    public function testIfMatchConditionSatisfied()
    {
        $response = new ConditionalResponse(new OKResponse(new PlainTextEntity('message')));

        $request = DefaultRequest::empty()->withHeader('If-Match', '"' . join('", "', [md5('message'), md5('a')]) . '"');
        $payload = $this->render($response, $request);
        Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", $payload);
    }

    public function testIfMatchConditionNotSatisfied()
    {
        $response = new ConditionalResponse(new OKResponse(new PlainTextEntity('message')));

        $request = DefaultRequest::empty()->withHeader('If-Match', '"123"');
        $payload = $this->render($response, $request);
        Assert::assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", $payload);
    }

    public function testIfNoneMatchConditionSatisfied()
    {
        $response = new ConditionalResponse(new OKResponse(new PlainTextEntity('message')));

        $request = DefaultRequest::empty()->withHeader('If-None-Match', '"' . join('", "', [md5('message'), md5('a')]) . '"');
        $payload = $this->render($response, $request);
        Assert::assertStringStartsWith("HTTP/1.1 304 Not Modified\r\n", $payload);
    }

    public function testIfNoneMatchConditionNotSatisfied()
    {
        $response = new ConditionalResponse(new OKResponse(new PlainTextEntity('message')));

        $request = DefaultRequest::empty()->withHeader('If-None-Match', '"123"');
        $payload = $this->render($response, $request);
        Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", $payload);
    }
}
