<?php

namespace App\Tests\Controller;

use App\Tests\Base;

class DefaultModeTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested /%s/\nExpected Page Title '%s', saw '%s'.",
        2 =>    "Test 2:\nRequested /%s/\nExpected HTTP Response Code %s, saw %s.",
        3 =>    "Test 3:\nRequested /%s/\nExpected Redirect Path %s, saw %s.",
    ];

    protected function getDefaultMode()
    {
        return 'signal_list';
    }

    public function test()
    {
        foreach ($this->getSystems() as $system) {
            $this->setNoRedirect();

            $this->client->request('GET', '/'.$system.'/');

            $expected =     'Redirecting to /'.$system.'/'.$this->getDefaultMode();
            $actual =       $this->getResponsePageTitle();
            $message =      $this->getError(1, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     302;
            $actual =       $this->getResponseStatusCode();
            $message =      $this->getError(2, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     '/'.$system.'/'.$this->getDefaultMode();
            $actual =       $this->getResponseRedirectLocation();
            $message =      $this->getError(3, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }
}
