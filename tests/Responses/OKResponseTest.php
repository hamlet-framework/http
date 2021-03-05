<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\PlainTextEntity;

class OKResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new OKResponse(new PlainTextEntity("message"));

        $payload = $this->render($response);
        $this->assertStringStartsWith("HTTP/1.1 200 OK\r\n", $payload);
    }
}
