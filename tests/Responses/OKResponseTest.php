<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\PlainTextEntity;
use PHPUnit\Framework\Assert;

class OKResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new OKResponse(new PlainTextEntity("message"));

        $payload = $this->render($response);
        Assert::assertStringStartsWith("HTTP/1.1 200 OK\r\n", $payload);
    }
}
