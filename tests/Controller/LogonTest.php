<?php

namespace App\Tests\Controller;

use App\Tests\Base;

class LogonTest extends Base
{
    const MESSAGES = [
        1 =>    "Testing %s/logoff: Expected HTTP response code %s, saw %s.",
        2 =>    "Testing %s/logon: Expected HTTP response code %s, saw %s.",
        3 =>    "Testing %s/logon: Expected page title '%s', saw '%s'.",
        4 =>    "Testing %s/logon: Expected %s logon form(s), saw %s.",
    ];

    public function test()
    {
        foreach ($this->getSystems() as $system) {
            $this->setNoRedirect();
            $this->client->request('GET', '/' . $system . '/logoff');
            $expected = 302;
            $actual =   $this->getResponseStatusCode();
            $message =  $this->getError(1, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $this->client->request('GET', '/' . $system . '/logon');
            $expected = 200;
            $actual =   $this->getResponseStatusCode();
            $message =  $this->getError(2, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected = strToUpper($system) . ' > Logon';
            $actual = $this->getResponsePageTitle();
            $message = $this->getError(3, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $forms = $this->getCrawler()->filter('form[name="form"]');
            $expected = 1;
            $actual = $forms->count();
            $message = $this->getError(4, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            foreach ($this->getAdminUsers() as $user => $data) {
                $this->setNoRedirect();
                $this->client->request('GET', '/' . $system . '/logoff');

                $this->client->request('GET', '/' . $system . '/logon');

                $forms = $this->getCrawler()->filter('form[name="form"]');
                $expected = 1;
                $actual = $forms->count();
                $message = $this->getError(4, [$system, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);

                $this->setYesRedirect();
                $form = $this
                    ->getCrawler()
                    ->filter('button#form_submit')
                    ->form(
                        [
                            'form[user]' => $user,
                            'form[password]' => $data['password']
                        ],
                        'POST'
                    );
                $this->client->submit($form);

                $forms = $this->getCrawler()->filter('form[name="form"]');
                $expected = $data['valid'] ? 0 : 1;
                $actual =   $forms->count();
                if ($expected != $actual) {
                    print "For user $user - ".print_r($data, true);
                    print $this->getResponseContent();
                }
                $message =  $this->getError(3, [$system, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);
            }
        }

    }
}
