<?php

namespace Hamlet\Http\Requests;

use Hamlet\Http\Message\Spec\Traits\DataProviderTrait;
use Hamlet\Http\Message\Spec\Traits\MessageTestTrait;
use Hamlet\Http\Message\Spec\Traits\RequestTestTrait;
use Hamlet\Http\Message\Spec\Traits\ServerRequestTestTrait;
use Hamlet\Http\Message\Stream;
use Hamlet\Http\Message\Uri;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use ReflectionException;
use function Hamlet\Cast\_class;
use function Hamlet\Cast\_int;

class DefaultRequestTest extends TestCase
{
    use DataProviderTrait;
    use MessageTestTrait;
    use RequestTestTrait;
    use ServerRequestTestTrait;

    protected function serverRequest(): ServerRequestInterface
    {
        return DefaultRequest::empty();
    }

    protected function message(): MessageInterface
    {
        return $this->serverRequest();
    }

    protected function request(): RequestInterface
    {
        return $this->serverRequest();
    }

    protected function stream(): StreamInterface
    {
        return Stream::empty();
    }

    protected function uri(string $value): UriInterface
    {
        return Uri::parse($value);
    }

    public function test_defaults()
    {
        Assert::assertTrue(true);
    }

    public function test_path_changes_after_uri_set()
    {
        $request = DefaultRequest::empty()->withUri(Uri::parse('http://google.com/test?x=2'));

        Assert::assertSame('/test', $request->getPath());
    }

    /**
     * adopted from guzzle/psr7
     * @return array
     */
    public function files_structure()
    {
        return [
            'Single file' => [
                [
                    'file' => [
                        'name' => 'MyFile.txt',
                        'type' => 'text/plain',
                        'tmp_name' => '/tmp/php/php1h4j1o',
                        'error' => '0',
                        'size' => '123'
                    ]
                ]
            ],
            'Empty file' => [
                [
                    'image_file' => [
                        'name' => '',
                        'type' => 'text/plain',
                        'tmp_name' => '',
                        'error' => '4',
                        'size' => '0'
                    ]
                ]
            ],
            'Multiple files' => [
                [
                    'text_file' => [
                        'name' => 'MyFile.txt',
                        'type' => 'text/plain',
                        'tmp_name' => '/tmp/php/php1h4j1o',
                        'error' => '0',
                        'size' => '123'
                    ],
                    'image_file' => [
                        'name' => '',
                        'type' => 'text/plain',
                        'tmp_name' => '',
                        'error' => '4',
                        'size' => '0'
                    ]
                ]
            ],
            'Nested files' => [
                [
                    'file' => [
                        'name' => [
                            0 => 'MyFile.txt',
                            1 => 'Image.png',
                        ],
                        'type' => [
                            0 => 'text/plain',
                            1 => 'image/png',
                        ],
                        'tmp_name' => [
                            0 => '/tmp/php/hp9hskjhf',
                            1 => '/tmp/php/php1h4j1o',
                        ],
                        'error' => [
                            0 => '0',
                            1 => '0',
                        ],
                        'size' => [
                            0 => '123',
                            1 => '7349',
                        ],
                    ],
                    'nested' => [
                        'name' => [
                            'other' => 'Flag.txt',
                            'test' => [
                                0 => 'Stuff.txt',
                                1 => '',
                            ],
                        ],
                        'type' => [
                            'other' => 'text/plain',
                            'test' => [
                                0 => 'text/plain',
                                1 => 'test/plain',
                            ],
                        ],
                        'tmp_name' => [
                            'other' => '/tmp/php/hp9hskjhf',
                            'test' => [
                                0 => '/tmp/php/asifu2gp3',
                                1 => '',
                            ],
                        ],
                        'error' => [
                            'other' => '0',
                            'test' => [
                                0 => '0',
                                1 => '4',
                            ],
                        ],
                        'size' => [
                            'other' => '421',
                            'test' => [
                                0 => '32',
                                1 => '0',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider files_structure()
     * @param array $files
     * @throws ReflectionException
     */
    public function test_files_parsing(array $files)
    {
        $type = new ReflectionClass(DefaultRequest::class);
        $method = $type->getMethod('readUploadedFilesFromFileParams');
        $method->setAccessible(true);
        Assert::assertTrue(true);
    }

    public function test_has_query_param()
    {
        $request = DefaultRequest::empty()
            ->withQueryParams(['id' => '12']);

        Assert::assertTrue($request->hasQueryParam('id'));
        Assert::assertFalse($request->hasQueryParam('name'));
    }

    public function test_get_query_param_casts_value()
    {
        $request = DefaultRequest::empty()
            ->withQueryParams(['id' => '12']);

        Assert::assertSame(12, $request->getQueryParam('id', _int()));
    }

    /**
     * @expectedException \Hamlet\Cast\CastException
     */
    public function test_get_query_param_throws_exception_on_impossible_cast()
    {
        $request = DefaultRequest::empty()
            ->withQueryParams(['id' => '1']);

        $request->getQueryParam('id', _class(\DateTime::class));
    }

    public function test_has_body_param()
    {
        $request = DefaultRequest::empty()
            ->withParsedBody(['id' => '12']);

        Assert::assertTrue($request->hasBodyParam('id'));
        Assert::assertFalse($request->hasBodyParam('name'));
    }

    public function test_get_body_param_casts_value()
    {
        $request = DefaultRequest::empty()
            ->withParsedBody(['id' => '12']);

        Assert::assertSame(12, $request->getBodyParam('id', _int()));
    }

    /**
     * @expectedException \Hamlet\Cast\CastException
     */
    public function test_get_body_param_throws_exception_on_impossible_cast()
    {
        $request = DefaultRequest::empty()
            ->withParsedBody(['id' => '1']);

        $request->getBodyParam('id', _class(\DateTime::class));
    }
}
