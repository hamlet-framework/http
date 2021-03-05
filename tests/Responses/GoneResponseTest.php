<?php

namespace Hamlet\Http\Responses;

class GoneResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new GoneResponse();

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 410 Gone", trim($payload));
    }
}
