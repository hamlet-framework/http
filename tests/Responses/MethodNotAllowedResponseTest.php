<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class MethodNotAllowedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new MethodNotAllowedResponse('GET', 'PUT');

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 405 Method Not Allowed\r\nAllow: GET, PUT", trim($payload));
    }
}
