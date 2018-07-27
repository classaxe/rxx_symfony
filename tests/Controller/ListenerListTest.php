<?php

namespace App\Tests\Controller;

use App\Tests\Base;
use App\Repository\TypeRepository;

class ListenerListTest extends Base
{
    const MESSAGES = [
        1 =>    "Test 1:\nRequested %s/listeners as %s:\nExpected HTTP response code %s, saw %s.",
        2 =>    "Test 2:\nRequested %s/listeners as %s:\nExpected page title '%s', saw '%s'.",
        3 =>    "Test 3:\nRequested %s/listeners as %s:\nExpected %s region selector(s), saw %s.",
        4 =>    "Test 4:\nRequested %s/listeners as %s with one signal type selected:\nExpected %s result column(s), saw %s.",
        5 =>    "Test 5:\nRequested %s/listeners as %s:\nExpected greater than %s result row(s), saw %s.",
        6 =>    "Test 6:\nRequested %s/listeners as %s with all signal types selected:\nExpected %s result column(s), saw %s.",
        7 =>    "Test 7:\nRequested %s/listeners as %s with query matching nothing:\nExpected message '%s', saw '%s'.",
    ];

    const COLS_MIN =    14;     // Only NDBs selected
    const COLS_MAX =    20;     // All types selected
    const COLS_ADMIN =  4;      // Additional colunms for admin users

    public function testPublic()
    {
        $this->setUserPublic();
        $this->common('public');
    }

    public function testAdmin()
    {
        $this->setUserAdmin();
        $this->common('admin');
        $this->setUserPublic();
    }

    private function common($usertype = false)
    {
        foreach ($this->getSystems() as $system) {
            $this->client->request('GET', '/' . $system . '/listeners');
            $expected =     200;
            $actual =       $this->getResponseStatusCode();
            $message =      $this->getError(1, [$system, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $expected =     strToUpper($system) . ' > Listeners List';
            $actual =       $this->getResponsePageTitle();
            $message =      $this->getError(2, [$system, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $selectors =    $this->getCrawler()->filter('#form_region');
            $expected =     $system === 'rww' ? 1 : 0;
            $actual =       $selectors->count();
            $message =      $this->getError(3, [$system, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $headRow =      $this->getCrawler()->filter('table.listener.results thead tr')->eq(0);
            $expected =     static::COLS_MIN + ($usertype == 'admin' ? static::COLS_ADMIN : 0);
            $actual =       $headRow->children()->count();
            $message =      $this->getError(4, [$system, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $resultRows =   $this->getCrawler()->filter('table.listener.results tbody')->eq(0);
            $expected =     100;  // 'GreaterThan' clause is used to test
            $actual =       $resultRows->children()->count();
            $message =      $this->getError(5, [$system, $usertype, $expected, $actual]);
            $this->assertGreaterThan($expected, $actual, $message);

            $form = $this
                ->getCrawler()
                ->filter('button#form_submit')
                ->form(['form[types]' =>    array_values((new TypeRepository)->getAll())], 'POST');
            $this->client->submit($form);

            $headRow =      $this->getCrawler()->filter('table.listener.results thead tr')->eq(0);
            $expected =     static::COLS_MAX + ($usertype == 'admin' ? static::COLS_ADMIN : 0);
            $actual =       $headRow->children()->count();
            $message =      $this->getError(6, [$system, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);

            $form = $this
                ->getCrawler()
                ->filter('button#form_submit')
                ->form(['form[filter]' =>    'AAAA'], 'POST');
            $this->client->submit($form);

            $noResults =    $this->getCrawler()->filter('p.no-results')->eq(0);
            $expected =     '(No listeners found matching your criteria)';
            $actual =       $noResults->text();
            $message =      $this->getError(7, [$system, $usertype, $expected, $actual]);
            $this->assertEquals($expected, $actual, $message);
        }
    }
}
