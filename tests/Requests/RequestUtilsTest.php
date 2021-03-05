<?php

namespace Hamlet\Http\Requests;

use PHPUnit\Framework\TestCase;

class RequestUtilsTest extends TestCase
{
    public function acceptLanguageHeaders()
    {
        return [
            ['*', ['*']],
            ['fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5', ['fr-CH', 'fr', 'en', 'de', '*']],
            ['de', ['de']],
            ['de-CH', ['de-CH']],
            ['en-US,en;q=0.5', ['en-US', 'en']],
            ['en;q=0.5,en-US', ['en-US', 'en']],
            [';', []],
            ['Sat, 29 Oct 1994 19:43:31 GMT', ['Sat']],
            ['😀😁😂,en;q=0.5,ru-👓', ['en']],
            ["fr-CH,\nfr;q=0.9,\nen;\r\tq=0.8", ['fr-CH', 'fr', 'en']],
            ['de,en-US, zh-Hant-TW,     En-au;q=0.111, aZ_cYrl-aZ', ['de', 'en-US', 'zh-Hant-TW', 'aZ_cYrl-aZ', 'En-au']],
            ['en;q=1, fr;q=0.9, de;q=0.8, zh-Hans;q=0.7, zh-Hant;q=0.6, ja;q=0.5', ['en', 'fr', 'de', 'zh-Hans', 'zh-Hant', 'ja']]
        ];
    }

    /**
     * @dataProvider acceptLanguageHeaders()
     * @param string $header
     * @param array $locales
     */
    public function testParsingAcceptLanguageHeader(string $header, array $locales)
    {
        $this->assertEquals($locales, RequestUtils::parseAcceptLanguageHeader([$header]));
    }
}
