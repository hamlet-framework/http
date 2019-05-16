<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class ServerErrorResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new ServerErrorResponse();

        $payload = $this->render($response);
        Assert::assertStringStartsWith("HTTP/1.1 500 Internal Server Error\r\n", $payload);
    }
}
