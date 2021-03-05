<?php

namespace Hamlet\Http\Responses;

class NotFoundResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new NotFoundResponse();

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 404 Not Found\r\nCache-Control: private", trim($payload));
    }
}
