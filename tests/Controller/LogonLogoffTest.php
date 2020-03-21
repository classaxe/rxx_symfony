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

            $this->myClient->request('GET', '/en/' . $system . '/admin/logoff');
            $expected = 302;
            $actual =   $this->getMyResponseStatusCode();
            $message =  $this->getError(1, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $this->myClient->request('GET', '/en/' . $system . '/admin/logon');
            $expected = 200;
            $actual =   $this->getMyResponseStatusCode();
            $message =  $this->getError(2, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected = strToUpper($system) . ' > Logon';
            $actual = $this->getMyResponsePageTitle();
            $message = $this->getError(3, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $forms = $this->getMyCrawler()->filter('form[name="form"]');
            $expected = 1;
            $actual = $forms->count();
            $message = $this->getError(4, [$system, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $this->setYesRedirect();

            foreach ($this->getAdminUsers() as $user => $data) {
                $this->myClient->request('GET', "/en/{$system}/admin/logoff");

                $form = $this
                    ->getMyCrawler()
                    ->filter('button#form_submit')
                    ->form(
                        [
                            'form[user]' => $user,
                            'form[password]' => $data['password']
                        ],
                        'POST'
                    );
                $this->myClient->submit($form);

                $expected = (
                    $data['valid'] ?
                        'Message: You have logged on as an Administrator. X'
                     :
                        'Error: Incorrect Username and / or Password. X'
                    );

                $errMsg =   $this->getMyCrawler()->filter('span#lastError');
                $okMsg =    $this->getMyCrawler()->filter('span#lastMessage');

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
