<?php

namespace src;

class GeoRouter
{
    private const COOKIE = 'userPreferredSite';

    private $defaultDomain = 'www.example.com';

    private $countryDomainMappings = [
        'US' => 'www.example.us',
        'CA' => 'www.example.us',
    ];

    private $url;

    private $ipCountry;

    public function __construct($url, $ipCountry)
    {
        $this->url = $url;
        $this->ipCountry = $ipCountry;
    }

    public function getDomainForIpCountry() : string
    {
        if (array_key_exists($this->ipCountry, $this->countryDomainMappings)) {
            return $this->countryDomainMappings[$this->ipCountry];
        }

        return $this->defaultDomain;
    }

    public function requestNeedsRedirect() : bool
    {
        if ($this->isCookieSet()) {
            return !$this->requestedHostMatchesCookieCountry();
        }

        return !$this->requestedHostMatchesIpCountry();
    }

    public function redirectTo() : string
    {
        if ($this->isCookieSet()) {
            return $this->getCookie();
        }

        return $this->getDomainForIpCountry();
    }

    public function setCookie($value) : bool
    {
        return setcookie(Self::COOKIE, $value);
    }

    private function getCookie() : string
    {
        return $_COOKIE[Self::COOKIE];
    }

    private function isCookieSet() : bool
    {
        return isset($_COOKIE[Self::COOKIE]) && $_COOKIE[Self::COOKIE] !== null;
    }

    private function requestedHostMatchesCookieCountry() : bool
    {
        return $this->getHost() === $this->getCookie();
    }

    private function requestedHostMatchesIpCountry() : bool
    {
        return $this->getHost() === $this->getDomainForIpCountry();
    }

    private function getHost()
    {
        return $this->url;
    }
}