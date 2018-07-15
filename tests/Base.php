<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class Base extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $client = static::createClient();
        $this->client = $client;
    }

    protected function getSystems()
    {
        return ['reu', 'rna', 'rww'];
    }

    protected function getVisitors()
    {
        return [
            'CAN'   =>  ['IP' => '206.248.171.206', 'system' => 'rna'],     // Canada, Ontario
            'USA'   =>  ['IP' => '72.130.194.78',   'system' => 'rna'],     // USA, Minnesota
            'ENG'   =>  ['IP' => '213.219.36.56',   'system' => 'reu'],     // England, London
            'RUS'   =>  ['IP' => '95.31.18.119',    'system' => 'reu'],     // Russia, Moscow
            'AUS'   =>  ['IP' => '202.86.32.122',   'system' => 'rww'],     // Brisbane, Australia
        ];
    }

    protected function getCrawler()
    {
        return $this->client->getCrawler();
    }

    protected function filter($match)
    {
        return $this->getCrawler()->filter($match);
    }

    protected function getError($id = null, $args = [])
    {
        return sprintf('ERROR '.static::MESSAGES[$id], ...$args);
    }

    protected function getResponse()
    {
        return $this->client->getResponse();
    }

    protected function getResponseContent()
    {
        return $this->getResponse()->getContent();
    }

    protected function getResponseRedirectLocation()
    {
        return $this->getResponse()->headers->get('location');
    }

    protected function getResponsePageTitle()
    {
        return $this->getCrawler()->filter('title')->eq(0)->text();
    }

    protected function getResponseStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    protected function setNoRedirect()
    {
        $this->client->setMaxRedirects(1);
        $this->client->followRedirects(false);
        return $this;
    }

    protected function setVisitorIP($IP)
    {
        putenv('PHPUNIT_CLIENT_IP='.$IP);
        return $this;
    }
}
