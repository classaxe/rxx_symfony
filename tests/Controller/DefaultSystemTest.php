<?php

namespace App\Tests\Controller;

use App\Tests\Base;

class DefaultSystemTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested /\nExpected Page Title '%s', saw '%s'.",
        2 =>    "Test 2:\nRequested /\nExpected HTTP Response Code %s, saw %s.",
        3 =>    "Test 3:\nRequested /\nExpected Redirect Path %s, saw %s.",
    ];

    public function testAllVisitors()
    {
        foreach ($this->getVisitors() as $country => $profile) {
            $this
                ->setVisitorIP($profile['IP'])
                ->setNoRedirect();

            $this->client->request('GET', '/');

            $expected =     'Redirecting to /'.$profile['system'].'/';
            $actual =       $this->getResponsePageTitle();
            $message =      $this->getError(1, [$expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     302;
            $actual =       $this->getResponseStatusCode();
            $message =      $this->getError(2, [$expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     '/'.$profile['system'].'/';
            $actual =       $this->getResponseRedirectLocation();
            $message =      $this->getError(3, [$expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }
}
