<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\PlainTextEntity;

class NotModifiedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new NotModifiedResponse(new PlainTextEntity("message"));

        $payload = $this->render($response);
        $this->assertStringStartsWith("HTTP/1.1 304 Not Modified\r\n", $payload);
    }
}
