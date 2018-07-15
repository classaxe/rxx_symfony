<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Base;

class DefaultModeTest extends Base
{
    protected function getDefaultMode()
    {
        return 'signal_list';
    }

    public function test()
    {
        foreach ($this->getSystems() as $system) {
            $this->setNoRedirect();

            $this->client->request('GET', '/'.$system.'/');

            $this->assertEquals('Redirecting to /'.$system.'/'.$this->getDefaultMode(), $this->getResponsePageTitle());
            $this->assertEquals(302, $this->getResponseStatusCode());
            $this->assertEquals('/'.$system.'/'.$this->getDefaultMode(), $this->getResponseRedirectLocation());
        }
    }
}
