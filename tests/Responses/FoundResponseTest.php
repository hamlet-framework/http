<?php

namespace Hamlet\Http\Responses;

class FoundResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new FoundResponse("https://example.com");

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 302 Found\r\nLocation: https://example.com", trim($payload));
    }
}
