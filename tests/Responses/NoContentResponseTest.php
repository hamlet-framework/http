<?php

namespace Hamlet\Http\Responses;

class NoContentResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new NoContentResponse();

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 204 No Content", trim($payload));
    }
}
