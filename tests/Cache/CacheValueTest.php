<?php

namespace Hamlet\Http\Cache;

use PHPUnit\Framework\TestCase;

class CacheValueTest extends TestCase
{
    public function test_extend_expiry()
    {
        $value = new CacheValue('content', 10, 1000);
        $this->assertEquals(1100, $value->extendExpiry(1100)->expiry());
        $this->assertEquals(1000, $value->expiry());
    }
}
