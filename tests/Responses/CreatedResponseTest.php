<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class CreatedResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new CreatedResponse('http://example.com');

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 201 Created\r\nLocation: http://example.com", trim($payload));
    }
}
