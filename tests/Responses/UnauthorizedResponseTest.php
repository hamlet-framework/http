<?php

namespace Hamlet\Http\Responses;

class UnauthorizedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new UnauthorizedResponse();

        $payload = $this->render($response);
        $this->assertEquals('HTTP/1.1 401 Unauthorized', trim($payload));
    }
}
