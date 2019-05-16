<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class ForbiddenResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new ForbiddenResponse();

        $payload = $this->render($response);
        Assert::assertEquals('HTTP/1.1 403 Forbidden', trim($payload));
    }
}
