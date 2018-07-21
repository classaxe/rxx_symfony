<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Base;

class DefaultModeTest extends Base
{
    const MESSAGES = [
        1 =>    "Testing /%s/ Expected page title '%s', saw '%s'.",
        2 =>    "Testing /%s/ Expected HTTP response code %s, saw %s.",
        3 =>    "Testing /%s/ Expected redirect path %s, saw %s.",
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
