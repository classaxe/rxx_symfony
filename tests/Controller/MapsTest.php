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
        5 =>    "Test 5:\nRequested %s\nExpected %d Countries and SP links, saw %d.",
    ];

    public function testAllSystems()
    {
        foreach ($this->getSystems() as $system) {
            $maps =         $this->getMapsForSystem($system);
            $url = '/en/'.$system.'/maps';
            $this->myClient->request('GET', $url);

            $expected =     strtoupper($system).' > Maps';
            $actual =       $this->getMyResponsePageTitle();
            $message =      $this->getError(1, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $links =        $this->getMyCrawler()->filter('.quicklinks a');
            $expected =     count($maps);
            $actual =       $links->count();
            $message =      $this->getError(2, [$url,  $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $regions =      $this->getMyCrawler()->filter('.help .header h3');
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
                $url = '/en/'.$system.'/maps/' . $area;
                $this->myClient->request('GET', $url);

                $expected = strToUpper($system).' > '.$mapDetails['mode'];
                $actual = $this->getMyResponsePageTitle();
                $message = $this->getError(4, [$url, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);

                $maplinks = $this->getMyCrawler()->filter('.header .links a[data-popup="1"]');
                $expected = (isset($mapDetails['countryBtn']) ? 1 : 0);
                $expected += (isset($mapDetails['stateBtn']) ? 1 : 0);
                $actual = $maplinks->count();
                $message = $this->getError(5, [$url, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);
            }
        }
    }
}
