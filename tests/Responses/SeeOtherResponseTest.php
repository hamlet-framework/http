<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\PlainTextEntity;
use PHPUnit\Framework\Assert;

class SeeOtherResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new SeeOtherResponse("https://example.com/?a=1");

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 303 See Other\r\nLocation: https://example.com/?a=1", trim($payload));
    }
}
