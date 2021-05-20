<?php

namespace Hamlet\Http\Entities;

use PHPUnit\Framework\TestCase;

class JsonEntityTest extends TestCase
{
    public function test_json_entity()
    {
        $entity = new JsonEntity([
            'a' => 12,
            'b' => 'test',
            'c' => null
        ]);
        $this->assertEquals('{"a":12,"b":"test","c":null}', $entity->getContent());
        $this->assertEquals('410dbe89aa0ccbca6e78708b4aad4972', $entity->getKey());
        $this->assertStringStartsWith('application/json', $entity->getMediaType());
        $this->assertEquals(0, $entity->getCachingTime());
        $this->assertNull($entity->getContentLanguage());
    }
}