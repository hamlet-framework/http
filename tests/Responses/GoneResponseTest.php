<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class GoneResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new GoneResponse();

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 410 Gone", trim($payload));
    }
}
