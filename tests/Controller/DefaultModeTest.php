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
        return 'signals';
    }

    public function testAllSystems()
    {
        foreach ($this->getSystems() as $system) {
            $this->setNoRedirect();

            $this->myClient->request('GET', '/en/'.$system.'/');

            $expected =     'Redirecting to /en/'.$system.'/'.$this->getDefaultMode();
            $actual =       $this->getMyResponsePageTitle();
            $message =      $this->getError(1, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     302;
            $actual =       $this->getMyResponseStatusCode();
            $message =      $this->getError(2, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     '/en/'.$system.'/'.$this->getDefaultMode();
            $actual =       $this->getMyResponseRedirectLocation();
            $message =      $this->getError(3, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }
}
