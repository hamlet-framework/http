<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class UnsupportedMediaTypeResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new UnsupportedMediaTypeResponse();

        $payload = $this->render($response);
        Assert::assertEquals('HTTP/1.1 415 Unsupported Media Type', trim($payload));
    }
}
