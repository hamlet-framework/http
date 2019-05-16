<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class AcceptedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new AcceptedResponse();

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 202 Accepted", trim($payload));
    }
}
