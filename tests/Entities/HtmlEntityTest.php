<?php

namespace Hamlet\Http\Entities;

use PHPUnit\Framework\TestCase;

class HtmlEntityTest extends TestCase
{
    public function test_html_entity()
    {
        $entity = new HtmlEntity('<html></html>');
        $this->assertEquals('<html></html>', $entity->getContent());
        $this->assertEquals('c83301425b2ad1d496473a5ff3d9ecca', $entity->getKey());
        $this->assertStringStartsWith('text/html', $entity->getMediaType());
        $this->assertEquals(0, $entity->getCachingTime());
        $this->assertNull($entity->getContentLanguage());
    }
}