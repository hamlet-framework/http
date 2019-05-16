<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class MovedPermanentlyResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new MovedPermanentlyResponse('https://example.com/1');

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 301 Moved Permanently\r\nLocation: https://example.com/1", trim($payload));
    }
}
