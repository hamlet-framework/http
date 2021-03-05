<?php

namespace Hamlet\Http\Responses;

class BadRequestResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new BadRequestResponse();

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 400 Bad Request", trim($payload));
    }
}
