<?php

namespace Hamlet\Http\Responses;

class ForbiddenResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new ForbiddenResponse();

        $payload = $this->render($response);
        $this->assertEquals('HTTP/1.1 403 Forbidden', trim($payload));
    }
}
