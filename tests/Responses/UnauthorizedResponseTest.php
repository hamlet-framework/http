<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class UnauthorizedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new UnauthorizedResponse();

        $payload = $this->render($response);
        Assert::assertEquals('HTTP/1.1 401 Unauthorized', trim($payload));
    }
}
