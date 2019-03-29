<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use src\GeoRouter;

class GeoRouterTest extends TestCase
{
    public function setUp()
    {
        require_once __DIR__ . '/../src/GeoRouter.php';
    }

    public function testReturnsCorrectDomains() : void
    {
        $geoRouter = new GeoRouter('www.example.com', 'DE');
        $this->assertEquals('www.example.com', $geoRouter->getDomainForIpCountry());
        $this->assertFalse($geoRouter->requestNeedsRedirect());

        $geoRouter = new GeoRouter('www.example.us', 'DE');
        $this->assertEquals('www.example.com', $geoRouter->getDomainForIpCountry());
        $this->assertTrue($geoRouter->requestNeedsRedirect());

        $geoRouter = new GeoRouter('www.example.com', 'GB');
        $this->assertEquals('www.example.com', $geoRouter->getDomainForIpCountry());
        $this->assertFalse($geoRouter->requestNeedsRedirect());

        $geoRouter = new GeoRouter('www.example.us', 'US');
        $this->assertEquals('www.example.us', $geoRouter->getDomainForIpCountry());
        $this->assertFalse($geoRouter->requestNeedsRedirect());

        $geoRouter = new GeoRouter('www.example.us', 'CA');
        $this->assertEquals('www.example.us', $geoRouter->getDomainForIpCountry());
        $this->assertFalse($geoRouter->requestNeedsRedirect());

        $geoRouter = new GeoRouter('www.example.com', 'CA');
        $this->assertEquals('www.example.us', $geoRouter->getDomainForIpCountry());
        $this->assertTrue($geoRouter->requestNeedsRedirect());

        $geoRouter = new GeoRouter('www.example.us', 'US');
        $this->assertEquals('www.example.us', $geoRouter->getDomainForIpCountry());
        $this->assertFalse($geoRouter->requestNeedsRedirect());
    }


    /**
     * @dataProvider getSpecificationTests
     */
    public function testItReturnsCorrectStuff($domain, $ipCountry, $cookie, $expectedRedirect) : void
    {
        unset($_COOKIE['userPreferredSite']);

        $geoRouter = new GeoRouter($domain, $ipCountry);

        if ($cookie) {
            //$geoRouter->setCookie($cookie);
            $_COOKIE['userPreferredSite'] = $cookie;
        }

        if ($expectedRedirect) {
            $this->assertTrue($geoRouter->requestNeedsRedirect());
            $this->assertEquals($expectedRedirect, $geoRouter->redirectTo());
        } else {
            $this->assertFalse($geoRouter->requestNeedsRedirect());
        }
    }

    public function getSpecificationTests() : array
    {
        // domain, ipCountry, cookie, expectedRedirect
        return [
            ['www.example.us', 'US', false, false],
            ['www.example.us', 'DE', false, 'www.example.com'],
            ['www.example.com', 'DE', false, false],
            ['www.example.com', 'US', false, 'www.example.us'],
            ['www.example.us', 'US', 'www.example.us', false],
            ['www.example.us', 'DE', 'www.example.us', false],
            ['www.example.com', 'US', 'www.example.us', 'www.example.us'],
            ['www.example.com', 'DE', 'www.example.com', false],
            ['www.example.com', 'US', 'www.example.com', false],
            ['www.example.us', 'DE', 'www.example.com', 'www.example.com'],
            ['www.example.us', 'US', 'www.example.com', 'www.example.com'],
            ['www.example.com', 'CA', false, 'www.example.us'],
            ['www.example.com', 'GB', false, false],
        ];
    }
}
