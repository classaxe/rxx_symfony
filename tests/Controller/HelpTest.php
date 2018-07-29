<?php

namespace App\Tests\Controller;

use App\Tests\Base;

class HelpTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested %s\nExpected Page Title '%s', saw '%s'.",
        2 =>    "Test 2:\nRequested %s\nExpected %d Quicklinks, saw %d."
    ];

    public function testAllSystems()
    {
        foreach ($this->getSystems() as $system) {
            $url = '/'.$system.'/help';
            $this->client->request('GET', $url);

            $expected =     strtoupper($system).' > Help';
            $actual =       $this->getResponsePageTitle();
            $message =      $this->getError(1, [$url, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $links =        $this->getCrawler()->filter('.quicklinks a');
            $expected =     8;
            $actual =       $links->count();
            $message =      $this->getError(2, [$url,  $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }
}
