<?php

namespace Hamlet\Http\Responses;

class TemporaryRedirectResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new TemporaryRedirectResponse('http://example.com');

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 307 Temporary Redirect\r\nCache-Control: private\r\nLocation: http://example.com", trim($payload));
    }
}
