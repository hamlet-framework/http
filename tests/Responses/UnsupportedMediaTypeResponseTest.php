<?php

namespace Hamlet\Http\Responses;

class UnsupportedMediaTypeResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new UnsupportedMediaTypeResponse();

        $payload = $this->render($response);
        $this->assertEquals('HTTP/1.1 415 Unsupported Media Type', trim($payload));
    }
}
