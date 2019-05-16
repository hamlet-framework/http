<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class PreconditionFailedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new PreconditionFailedResponse();

        $payload = $this->render($response);
        Assert::assertStringStartsWith("HTTP/1.1 412 Precondition Failed\r\n", $payload);
    }
}
