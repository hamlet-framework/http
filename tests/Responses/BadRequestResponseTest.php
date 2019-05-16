<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class BadRequestResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new BadRequestResponse();

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 400 Bad Request", trim($payload));
    }
}
