<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class NotFoundResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new NotFoundResponse();

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 404 Not Found\r\nCache-Control: private", trim($payload));
    }
}
