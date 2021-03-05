<?php

namespace Hamlet\Http\Responses;

class ServerErrorResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new ServerErrorResponse();

        $payload = $this->render($response);
        $this->assertStringStartsWith("HTTP/1.1 500 Internal Server Error\r\n", $payload);
    }
}
