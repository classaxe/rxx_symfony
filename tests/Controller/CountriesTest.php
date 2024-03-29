<?php

namespace App\Tests\Controller;

use App\Tests\Base;

class CountriesTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested %s\nExpected Page Title '%s', saw '%s'.",
        2 =>    "Test 2:\nRequested %s\nExpected %d Quicklinks, saw %d.",
        3 =>    "Test 3:\nRequested %s\nExpected %d Regions, saw %d.",
        4 =>    "Test 4:\nRequested %s\nExpected %d Regions with map links, saw %d.",
        5 =>    "Test 5:\nRequested %s\nExpected Page Title '%s', saw '%s'.",
        6 =>    "Test 6:\nRequested %s\nExpected %d Quicklinks, saw %d.",
        7 =>    "Test 7:\nRequested %s\nExpected %d Regions, saw %d.",
        8 =>    "Test 8:\nRequested %s\nExpected %d Regions with map links, saw %d.",
        9 =>    "Test 9:\nRequested %s\nExpected Zone Title '%s', saw '%s'.",
       10 =>    "Test 10:\nRequested %s\nExpected %d Quicklinks, saw %d.",
    ];

    public function testAllSystems()
    {
        foreach ($this->getSystems() as $system) {
            $url = '/en/'.$system.'/countries';
            $this->myClient->request('GET', $url);

            $expected =     strtoupper($system).' | Country Code Locator';
            $actual =       $this->getMyResponsePageTitle();
            $message =      $this->getError(1, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $links =        $this->getMyCrawler()->filter('.quicklinks a');
            $expected =     count($this->getRegions());
            $actual =       $links->count();
            $message =      $this->getError(2, [$url,  $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $regions =      $this->getMyCrawler()->filter('.zone .header h2');
            $expected =     count($this->getRegions());
            $actual =       $regions->count();
            $message =      $this->getError(3, [$url,  $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $maplinks =     $this->getMyCrawler()->filter('.zone .header .links a:contains("Map")');
            $expected =     $this->countRegionsWithMaps();
            $actual =       $maplinks->count();
            $message =      $this->getError(4, [$url,  $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }

    public function testRwwEachRegion()
    {
        foreach ($this->getRegions() as $region => $settings) {
            $url = '/en/rww/countries/' . $region;
            $this->myClient->request('GET', $url);

            $expected = 'RWW | Country Code Locator';
            $actual = $this->getMyResponsePageTitle();
            $message = $this->getError(5, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $links = $this->getMyCrawler()->filter('.quicklinks a');
            $expected = 0;
            $actual = $links->count();
            $message = $this->getError(6, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $regions = $this->getMyCrawler()->filter('.zone .header h2');
            $expected = 1;
            $actual = $regions->count();
            $message = $this->getError(7, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $maplinks = $this->getMyCrawler()->filter('.zone .header .links a:contains("Map")');
            $expected = ($settings['map'] ? 1 : 0);
            $actual = $maplinks->count();
            $message = $this->getError(8, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $regions = $this->getMyCrawler()->filter('.zone .header h2');
            $expected = $settings['title'];
            $actual = $regions->eq(0)->text();
            $message = $this->getError(9, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }

    public function testRwwAllRegions()
    {
        $allRegions = array_keys($this->getRegions());
        $url = '/en/rww/countries/'.implode(',', $allRegions);
        $this->myClient->request('GET', $url);

        $links =        $this->getMyCrawler()->filter('.quicklinks a');
        $expected =     count($allRegions);
        $actual =       $links->count();
        $message =      $this->getError(10, [$url,  $expected, $actual]);
        $this->assertEquals($expected, $actual, $message);
    }
}
