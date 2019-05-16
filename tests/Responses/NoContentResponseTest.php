<?php

namespace Hamlet\Http\Responses;

use PHPUnit\Framework\Assert;

class NoContentResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new NoContentResponse();

        $payload = $this->render($response);
        Assert::assertEquals("HTTP/1.1 204 No Content", trim($payload));
    }
}
