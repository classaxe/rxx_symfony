<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Base;

class DefaultSystemTest extends Base
{
    const MESSAGES = [
        1 =>    "Testing / Expected page title '%s', saw '%s'.",
        2 =>    "Testing / Expected HTTP response code %s, saw %s.",
        3 =>    "Testing / Expected redirect path %s, saw %s.",
    ];

    public function test()
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
