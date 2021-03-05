<?php

namespace Hamlet\Http\Responses;

class WebSocketUpgradeResponseTest extends ResponseTestCase
{
    public function testCodeAndMessage()
    {
        $response = new WebSocketUpgradeResponse("123");

        $payload = $this->render($response);
        $this->assertEquals("HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: 123\r\nSec-WebSocket-Version: 13\r\nKeepAlive: off", trim($payload));
    }
}
