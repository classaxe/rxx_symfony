<?php

namespace App\Tests\Controller;

use App\Tests\Base;

class MapsTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested %s\nExpected Page Title '%s', saw '%s'.",
        2 =>    "Test 2:\nRequested %s\nExpected %d Quicklinks, saw %d.",
        3 =>    "Test 3:\nRequested %s\nExpected %d Maps, saw %d.",
        4 =>    "Test 1:\nRequested %s\nExpected Page Title '%s', saw '%s'.",
        5 =>    "Test 5:\nRequested %s\nExpected %d Countries links, saw %d.",
        6 =>    "Test 6:\nRequested %s\nExpected %d State/Provinces links, saw %d.",
    ];

    public function testAllSystems()
    {
        foreach ($this->getSystems() as $system) {
            $maps =         $this->getMapsForSystem($system);
            $url = '/en/'.$system.'/maps';
            $this->client->request('GET', $url);

            $expected =     strtoupper($system).' > Maps';
            $actual =       $this->getResponsePageTitle();
            $message =      $this->getError(1, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $links =        $this->getCrawler()->filter('.quicklinks a');
            $expected =     count($maps);
            $actual =       $links->count();
            $message =      $this->getError(2, [$url,  $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $regions =      $this->getCrawler()->filter('.map .header h3');
            $expected =     count($maps);
            $actual =       $regions->count();
            $message =      $this->getError(3, [$url,  $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }

    public function testRwwEachMap()
    {
        foreach ($this->getSystems() as $system) {
            foreach ($this->getMapsForSystem($system) as $area) {
                $mapDetails = $this->getMap($area);
                $url = '/en/'.$system.'/map/' . $area;
                $this->client->request('GET', $url);

                $expected = strToUpper($system).' > '.$mapDetails['mode'];
                $actual = $this->getResponsePageTitle();
                $message = $this->getError(4, [$url, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);

                $maplinks = $this->getCrawler()->filter('.header .links a[data-type="itu"]');
                $expected = (isset($mapDetails['countryBtn']) ? 1 : 0);
                $actual = $maplinks->count();
                $message = $this->getError(5, [$url, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);

                $maplinks = $this->getCrawler()->filter('.header .links a[data-type="sp"]');
                $expected = (isset($mapDetails['stateBtn']) ? 1 : 0);
                $actual = $maplinks->count();
                $message = $this->getError(6, [$url, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);
            }
        }
    }
}
