<?php

namespace Hamlet\Http\Template;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class MustacheRendererTest extends TestCase
{
    public function testSimpleTemplate()
    {
        $renderer = new MustacheRenderer();
        $path = __DIR__ . '/hello.mustache';
        $data = ['name' => 'Vladimir'];

        Assert::assertEquals('Hello, Vladimir!', trim($renderer->render($data, $path)));
    }
}
