<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\PlainTextEntity;
use PHPUnit\Framework\Assert;

class SimpleOKResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new SimpleOKResponse(new PlainTextEntity("message"));

        $payload = $this->render($response);
        Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", $payload);
    }
}
