<?php

namespace App\Tests\Controller;

use App\Tests\Base;
use App\Repository\TypeRepository;

class ListenersTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested %s as %s:\nExpected HTTP response code %s, saw %s.",
        2 =>    "Test 2:\nRequested %s as %s:\nExpected page title '%s', saw '%s'.",
        3 =>    "Test 3:\nRequested %s as %s:\nExpected %s region selector(s), saw %s.",
        4 =>    "Test 4:\nRequested %s as %s with one signal type selected:\nExpected %s result column(s), saw %s.",
        5 =>    "Test 5:\nRequested %s as %s:\nExpected greater than %s result row(s), saw %s.",
        6 =>    "Test 6:\nRequested %s as %s with all signal types selected:\nExpected %s result column(s), saw %s.",
        7 =>    "Test 7:\nRequested %s as %s with query matching nothing:\nExpected message '%s', saw '%s'.",
    ];

    const COLS_MIN =    15;     // Only NDBs selected
    const COLS_MAX =    21;     // All types selected
    const COLS_ADMIN =  4;      // Additional colunms for admin users

    public function testAllSystemsPublic()
    {
        $this->setUserPublic();
        $this->common('public');
    }

    public function testAllSystemsAdmin()
    {
        $this->setUserAdmin();
        $this->common('admin');
        $this->setUserPublic();
    }

    private function common($usertype = false)
    {
        foreach ($this->getSystems() as $system) {
            $url = '/en/' . $system . '/listeners';
            $this->client->request('GET', $url);
            $expected =     200;
            $actual =       $this->getResponseStatusCode();
            $message =      $this->getError(1, [$url, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     strToUpper($system) . ' > Listeners List';
            $actual =       $this->getResponsePageTitle();
            $message =      $this->getError(2, [$url, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $selectors =    $this->getCrawler()->filter('#form_region');
            $expected =     $system === 'rww' ? 1 : 0;
            $actual =       $selectors->count();
            $message =      $this->getError(3, [$url, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $headRow =      $this->getCrawler()->filter('table.listener.results thead tr')->eq(0);
            $expected =     static::COLS_MIN + ($usertype == 'admin' ? static::COLS_ADMIN : 0);
            $actual =       $headRow->children()->count();
            $message =      $this->getError(4, [$url, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $resultRows =   $this->getCrawler()->filter('table.listener.results tbody')->eq(0);
            $expected =     10;  // 'GreaterThan' clause is used to test
            $actual =       $resultRows->children()->count();
            $message =      $this->getError(5, [$url, $usertype, $expected, $actual]);
            $this->assertGreaterThan($expected, $actual, $message);

            $form = $this
                ->getCrawler()
                ->filter('button#form_submit')
                ->form(['form[types]' =>    array_values((new TypeRepository)->getAllChoices())], 'POST');
            $this->client->submit($form);

            $headRow =      $this->getCrawler()->filter('table.listener.results thead tr')->eq(0);
            $expected =     static::COLS_MAX + ($usertype == 'admin' ? static::COLS_ADMIN : 0);
            $actual =       $headRow->children()->count();
            $message =      $this->getError(6, [$url, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $form = $this
                ->getCrawler()
                ->filter('button#form_submit')
                ->form(['form[filter]' =>    'AAAA'], 'POST');
            $this->client->submit($form);

            $noResults =    $this->getCrawler()->filter('p.no-results')->eq(0);
            $expected =     '(No listeners found matching your criteria)';
            $actual =       $noResults->text();
            $message =      $this->getError(7, [$url, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }
}
