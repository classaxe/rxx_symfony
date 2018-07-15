<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Base;

class DefaultSystemTest extends Base
{
    public function test()
    {
        foreach ($this->getVisitors() as $country => $profile) {
            $this
                ->setVisitorIP($profile['IP'])
                ->setNoRedirect();

            $this->client->request('GET', '/');

            $this->assertEquals('Redirecting to /'.$profile['system'].'/', $this->getResponsePageTitle());
            $this->assertEquals(302, $this->getResponseStatusCode());
            $this->assertEquals('/'.$profile['system'].'/', $this->getResponseRedirectLocation());
        }
    }
}
