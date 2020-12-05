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

    public function test_get_query_param_throws_exception_on_impossible_cast()
    {
        $request = DefaultRequest::empty()
            ->withQueryParams(['id' => '1']);

        $this->expectException(\Hamlet\Cast\CastException::class);
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

    public function test_get_body_param_throws_exception_on_impossible_cast()
    {
        $request = DefaultRequest::empty()
            ->withParsedBody(['id' => '1']);

        $this->expectException(\Hamlet\Cast\CastException::class);
        $request->getBodyParam('id', _class(\DateTime::class));
    }

    public function server_params()
    {
        return [
            [
                [
                    'USER' => 'nginx',
                    'HOME' => '/var/lib/nginx',
                    'HTTP_CONNECTION' => 'keep-alive',
                    'HTTP_X_FORWARDED_PROTO' => 'http',
                    'HTTP_X_FORWARDED_PORT' => '80',
                    'HTTP_X_FORWARDED_FOR' => '11.14.6.2',
                    'HTTP_USER_AGENT' => 'Mozilla/5.0',
                    'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
                    'HTTP_DNT' => '1',
                    'HTTP_COOKIE' => 'PHPSESSID=uebp1a6lmdjcvm7hhhcbm27ra7',
                    'HTTP_CACHE_CONTROL' => 'max-age=0',
                    'HTTP_ACCEPT_LANGUAGE' => 'en,ru;q=0.9,es;q=0.8,de;q=0.7',
                    'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
                    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                    'HTTP_HOST' => 'example.com',
                    'REDIRECT_STATUS' => '200',
                    'SERVER_NAME' => '',
                    'SERVER_PORT' => '80',
                    'SERVER_ADDR' => '10.0.2.53',
                    'REMOTE_PORT' => '1960',
                    'REMOTE_ADDR' => '10.0.2.122',
                    'SERVER_SOFTWARE' => 'nginx/1.14.1',
                    'GATEWAY_INTERFACE' => 'CGI/1.1',
                    'REQUEST_SCHEME' => 'http',
                    'SERVER_PROTOCOL' => 'HTTP/1.1',
                    'DOCUMENT_ROOT' => '/var/www/html/html',
                    'DOCUMENT_URI' => '/test.php',
                    'REQUEST_URI' => '/test.php?a=12',
                    'SCRIPT_NAME' => '/test.php',
                    'CONTENT_LENGTH' => '',
                    'CONTENT_TYPE' => '',
                    'REQUEST_METHOD' => 'GET',
                    'QUERY_STRING' => 'a=12',
                    'SCRIPT_FILENAME' => '/var/www/test.php',
                    'FCGI_ROLE' => 'RESPONDER',
                    'PHP_SELF' => '/test.php',
                    'REQUEST_TIME_FLOAT' => '1557814144.2578',
                    'REQUEST_TIME' => '1557814144',
                ],
                'http://example.com/test.php?a=12'
            ],
            [
                [
                    'USER' => 'nginx',
                    'HOME' => '/var/lib/nginx',
                    'HTTP_CONNECTION' => 'keep-alive',
                    'HTTP_X_FORWARDED_PROTO' => 'https',
                    'HTTP_X_FORWARDED_PORT' => '443',
                    'HTTP_X_FORWARDED_FOR' => '11.14.6.2',
                    'HTTP_USER_AGENT' => 'Mozilla/5.0',
                    'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
                    'HTTP_DNT' => '1',
                    'HTTP_COOKIE' => 'PHPSESSID=uebp1a6lmdjcvm7hhhcbm27ra7;',
                    'HTTP_CACHE_CONTROL' => 'max-age=0',
                    'HTTP_ACCEPT_LANGUAGE' => 'en,ru;q=0.9,es;q=0.8,de;q=0.7',
                    'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, br',
                    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                    'HTTP_HOST' => 'example.com',
                    'REDIRECT_STATUS' => '200',
                    'SERVER_NAME' => '',
                    'SERVER_PORT' => '80',
                    'SERVER_ADDR' => '10.0.2.53',
                    'REMOTE_PORT' => '44580',
                    'REMOTE_ADDR' => '10.0.1.104',
                    'SERVER_SOFTWARE' => 'nginx/1.14.1',
                    'GATEWAY_INTERFACE' => 'CGI/1.1',
                    'REQUEST_SCHEME' => 'http',
                    'SERVER_PROTOCOL' => 'HTTP/1.1',
                    'DOCUMENT_ROOT' => '/var/www/html/html',
                    'DOCUMENT_URI' => '/test.php',
                    'REQUEST_URI' => '/test.php?a=12',
                    'SCRIPT_NAME' => '/test.php',
                    'CONTENT_LENGTH' => '',
                    'CONTENT_TYPE' => '',
                    'REQUEST_METHOD' => 'GET',
                    'QUERY_STRING' => 'a=12',
                    'SCRIPT_FILENAME' => '/var/www/html/html/test.php',
                    'FCGI_ROLE' => 'RESPONDER',
                    'PHP_SELF' => '/test.php',
                    'REQUEST_TIME_FLOAT' => '1557814388.9157',
                    'REQUEST_TIME' => '1557814388'
                ],
                'https://example.com/test.php?a=12'
            ]
        ];
    }

    /**
     * @dataProvider server_params()
     * @param array $serverParams
     * @param string $uri
     * @throws ReflectionException
     */
    public function test_get_uri_from_server_params(array $serverParams, string $uri)
    {
        $type = new ReflectionClass(DefaultRequest::class);
        $method = $type->getMethod('readUriFromServerParams');
        $method->setAccessible(true);

        Assert::assertEquals($uri, (string) $method->invoke(null, $serverParams));
    }
}
