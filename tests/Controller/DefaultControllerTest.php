<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    const IP = [
        'CAN'   =>  '206.248.171.206',  // Canada, Ontario
        'USA'   =>  '72.130.194.78',    // USA, Minnesota
        'ENG'   =>  '213.219.36.56',    // England, London
        'RUS'   =>  '95.31.18.119',     // Russia, Moscow
        'AUS'   =>  '202.86.32.122'     // Brisbane, Australia
    ];

    private $client;

    public function testHome()
    {
        $this
            ->client()
            ->setNoRedirect()
            ->setVisitorCountry(static::IP['CAN'])
            ->doRequest('GET', '/');

        print
            "\r\n"
            .html_entity_decode($this->getResponseTitle())."\r\n"
            ."Status code is ".$this->getResponseStatusCode();
        print_r($this->getResponseContent());

        $this->assertEquals(200, $this->getResponseStatusCode());
    }

    private function client()
    {
        $this->client = static::createClient();
        return $this;
    }

    private function getResponse() {
        return $this->client->getResponse();
    }

    private function getResponseContent() {
        return $this->client->getResponse()->getContent();
    }

    private function getResponseTitle() {
        $res = preg_match(
            "/<title>(.*)<\/title>/siU",
            $this->client->getResponse(),
            $title_matches
        );
        if (!$res) {
            return null;
        }
        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                $title_matches[1]
            )
        );
    }

    private function getResponseStatusCode() {
        return $this->client->getResponse()->getStatusCode();
    }

    private function setNoRedirect()
    {
        $this->client->setMaxRedirects(1);
        $this->client->followRedirects(false);
        return $this;
    }

    private function doRequest(string $method, string $uri, array $parameters = array(), array $files = array(), array $server = array(), string $content = null, bool $changeHistory = true)
    {
        $this->client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
        return $this;
    }

    private function setVisitorCountry($country) {
        putenv('PHPUNIT_CLIENT_IP='.$country);
        return $this;
    }
}