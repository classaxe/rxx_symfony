<?php

namespace App\Tests\Controller;

use App\Tests\Base;

class LogonLogoffTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested %s/logoff\nExpected HTTP response code %s, saw %s.",
        2 =>    "Test 2:\nRequested %s/logon\nExpected HTTP response code %s, saw %s.",
        3 =>    "Test 3:\nRequested %s/logon\nExpected page title '%s', saw '%s'.",
        4 =>    "Test 4:\nRequested %s/logon\nExpected %s logon form(s), saw %s.",
        5 =>    "Test 5:\nRequested %s/logon for user '%s'\nExpected response '%s', saw '%s'.",
    ];

    public function testAllSystems()
    {
        foreach ($this->getSystems() as $system) {
            $this->setNoRedirect();

            $this->client->request('GET', '/' . $system . '/admin/logoff');
            $expected = 302;
            $actual =   $this->getResponseStatusCode();
            $message =  $this->getError(1, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $this->client->request('GET', '/' . $system . '/admin/logon');
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

            $this->setYesRedirect();

            foreach ($this->getAdminUsers() as $user => $data) {
                $this->client->request('GET', '/' . $system . '/admin/logoff');

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

                $expected = (
                    $data['valid'] ?
                        'You are now logged on as an Administrator and may perform administrative functions.'
                     :
                        'Error: Incorrect Username and / or Password.'
                    );

                $errMsg =   $this->getCrawler()->filter('div#lastError');
                $okMsg =    $this->getCrawler()->filter('p#success');

                $actual =   (
                    $errMsg->count() ?
                        $errMsg->text()
                    :
                        ($okMsg->count() ?
                            $okMsg->text()
                        :
                            ''
                        )
                    );
                $message =  $this->getError(5, [$system, $user, $expected, $actual]);
                $this->assertEquals($expected, $actual, $message);
            }
        }
    }
}
