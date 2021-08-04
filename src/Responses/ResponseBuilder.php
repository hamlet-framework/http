<?php

namespace Hamlet\Http\Responses;

use Hamlet\Http\Entities\Entity;
use RuntimeException;

class ResponseBuilder
{
    private ?int $statusCode = null;

    private ?Entity $entity = null;

    /**
     * @var array<string,array<string>>
     */
    private array $headers = [];

    /**
     * @var array<Cookie>
     */
    private array $cookies = [];

    private function __construct()
    {
    }

    public static function create(): ResponseBuilder
    {
        return new ResponseBuilder();
    }

    public function withStatusCode(int $code): ResponseBuilder
    {
        $this->statusCode = $code;
        return $this;
    }

    public function withEntity(Entity $entity): ResponseBuilder
    {
        $this->entity = $entity;
        return $this;
    }

    public function withHeader(string $name, string $value): ResponseBuilder
    {
        $this->headers[$name][] = $value;
        return $this;
    }

    public function withCookie(Cookie $cookie): ResponseBuilder
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    public function build(): Response
    {
        if ($this->statusCode == null) {
            throw new RuntimeException('Status code needs to be defined');
        }
        return new class($this->statusCode, $this->entity,  $this->entity !== null, $this->headers, $this->cookies) extends Response
        {
            /**
             * @param int                               $statusCode
             * @param Entity|null                       $entity
             * @param bool                              $embedEntity
             * @param array                             $headers
             * @psalm-param array<string,array<string>> $headers
             * @param Cookie[]                          $cookies
             * @psalm-param array<Cookie>               $cookies
             */
            public function __construct($statusCode, $entity, $embedEntity, array $headers, array $cookies)
            {
                parent::__construct($statusCode, $entity, $embedEntity, $headers, $cookies);
            }
        };
    }
}
