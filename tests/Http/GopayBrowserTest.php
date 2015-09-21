<?php

namespace GoPay\Http;

use Prophecy\Argument;

class GopayBrowserTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider provideRequest */
    public function testShouldCompleteRequest(
        $isProductionMode,
        $headers,
        $body,
        $expectedMethod,
        $expectedBaseUrl,
        $expectedAuthorization,
        $expectedBody
    ) {
        $browser = $this->prophesize('GoPay\Http\Browser');
        $browser->send(
            $expectedMethod,
            Argument::containingString($expectedBaseUrl),
            array_merge($headers, ['Authorization' => $expectedAuthorization]),
            $expectedBody
        )->shouldBeCalled();

        $gopay = new GopayBrowser(['isProductionMode' => $isProductionMode], $browser->reveal());
        $gopay->api('irrelevant url path', $headers, $body);
    }

    public function provideRequest()
    {
        return [
            'get json in production' => [
                true,
                ['Content-Type' => GopayBrowser::FORM, 'Authorization' => 'Bearer irrelevantToken'],
                null,
                'GET',
                'https://gate.gopay.cz/api/',
                'Bearer irrelevantToken',
                ''
            ],
            'send form in production' => [
                true,
                ['Content-Type' => GopayBrowser::FORM, 'Authorization' => 'Bearer irrelevantToken'],
                ['key' => 'value'],
                'POST',
                'https://gate.gopay.cz/api/',
                'Bearer irrelevantToken',
                'key=value'
            ],
            'send json in test' => [
                false,
                ['Content-Type' => GopayBrowser::JSON, 'Authorization' => ['user', 'pass']],
                ['key' => 'value'],
                'POST',
                'https://gw.sandbox.gopay.com/api/',
                'Basic dXNlcjpwYXNz',
                '{"key":"value"}'
            ]
        ];
    }
}
