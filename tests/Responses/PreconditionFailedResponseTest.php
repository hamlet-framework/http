<?php

namespace Hamlet\Http\Responses;

class PreconditionFailedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new PreconditionFailedResponse();

        $payload = $this->render($response);
        $this->assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", $payload);
    }
}
