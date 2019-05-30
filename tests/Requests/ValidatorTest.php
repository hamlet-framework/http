<?php

namespace Hamlet\Http\Requests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function entityTagMatches(): array
    {
        return [
            ['*', '"1"', true, true],
            ['"aa"', '"aa"', true, true],
            ['W/"1"', 'W/"1"', false, true],
            ['W/"1"', 'W/"2"', false, false],
            ['W/"1"', '"1"', false, true],
            ['"1"', '"1"', true, true]
        ];
    }

    /**
     * @dataProvider entityTagMatches()
     * @param string $tag1
     * @param string $tag2
     * @param bool $strongMatch
     * @param bool $weakMatch
     */
    public function testStrongMatch(string $tag1, string $tag2, bool $strongMatch, bool $weakMatch)
    {
        Assert::assertEquals($strongMatch, Validator::strongMatch($tag1, $tag2));
        Assert::assertEquals($weakMatch, Validator::weakMatch($tag1, $tag2));
    }
}
