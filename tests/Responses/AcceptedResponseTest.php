<?php

namespace Hamlet\Http\Responses;

class AcceptedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new AcceptedResponse();

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 202 Accepted", trim($payload));
    }
}
