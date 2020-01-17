<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class Base extends WebTestCase
{
    protected $myClient;

    protected $currentUserType = 'public';

    protected $currentRedirectStatus;

    protected function setUp()
    {
        $client = static::createClient();
        $this->myClient = $client;
    }

    protected function getCountriesHavingStates()
    {
        return [
            'aus' => ['title' => 'Australia',            'map' => 'map_au'],
            'can' => ['title' => 'Canada',               'map' => 'map_na'],
            'usa' => ['title' => 'USA',                  'map' => 'map_na']
        ];
    }

    protected function getMap($area)
    {
        return $this->getMaps()[$area];
    }

    protected function getMaps()
    {
        return [
            'af' => [
                'mode' =>           'African NDB List approved Country Codes',
                'countryBtn' =>     'Countries',
                'countryFilter' =>  'af',
                'map' =>            'af_map.gif',
                'shortName' =>      'Africa',
                'popup' =>          'map_af|width=646,height=652,resizable=1',
            ],
            'alaska' => [
                'mode' =>           'Beacons in Alaska',
                'map' =>            'map_alaska_beacons.gif',
                'text' =>           'OR... try the <a href="state_map/?simple=1&SP=AK">interactive map of Alaska</a>',
                'shortName' =>      'Alaska',
                'popup' =>          'map_alaska|width=466,height=443,resizable=1',
            ],
            'as' => [
                'mode' =>           'Asian NDB List Country Codes',
                'countryBtn' =>     'Countries',
                'countryFilter' =>  'as',
                'map' =>            'as_map.gif',
                'shortName' =>      'Asia',
                'popup' =>          'map_as|width=856,height=575,resizable=1',
            ],
            'au' => [
                'mode' =>           'Australian NDB List Country Codes',
                'stateBtn' =>       'Territories',
                'stateFilter' =>    'aus',
                'map' =>            'au_map.gif',
                'shortName' =>      'Australia',
                'popup' =>          'map_au|width=511,height=469,resizable=1',
            ],
            'eu' => [
                'mode' =>           'European NDB List Country Codes',
                'countryBtn' =>     'Countries',
                'countryFilter' =>  'eu',
                'map' =>            'eu_map.gif',
                'shortName' =>      'Europe',
                'popup' =>          'map_eu|width=704,height=696,resizable=1',
            ],
            'japan' => [
                'mode' =>           'Japanese NDB List Country Codes',
                'map' =>            'japan_map.gif',
                'countryBtn' =>     'Countries',
                'countryFilter' =>  'as',
                'shortName' =>      'Japan',
                'popup' =>          'map_japan|width=517,height=690,resizable=1',
            ],
            'na' => [
                'mode' =>           'North American NDB List Country Codes',
                'stateBtn' =>       'States',
                'stateFilter' =>    'can,usa',
                'countryBtn' =>     'Countries',
                'countryFilter' =>  'na',
                'map' =>            'na_map.gif',
                'shortName' =>      'North America',
                'popup' =>          'map_na|width=669,height=660,resizable=1',
            ],
            'pacific' => [
                'mode' =>           'Pacific Beacons Map',
                'map' =>            'pacific_map.gif',
                'countryBtn' =>     'Countries',
                'countryFilter' =>  'oc',
                'text' =>
                    '(Originally produced for <a href="/dx/ndb/log/steve/?mode=station_list&yyyymm=200307">'
                    .'Steve Ratzlaff\'s Pacific Report</a>)',
                'shortName' =>      'Pacific',
                'popup' =>          '|map_pacific|width=366,height=429,resizable=1',
            ],
            'polynesia' => [
                'mode' =>           'French Polynesian Beacons Map',
                'map' =>            'map_french_polynesia.gif',
                'shortName' =>      'French Polynesia',
                'popup' =>          'map_polynesia|width=458,height=440,resizable=1',
            ],
            'sa' => [
                'mode' =>           'South American NDB List Country Codes',
                'map' =>            'sa_map.gif',
                'countryBtn' =>     'Countries',
                'countryFilter' =>  'sa',
                'shortName' =>      'South America',
                'popup' =>          'map_sa|width=490,height=686,resizable=1',
            ]
        ];
    }

    protected function getMapsForSystems()
    {
        return [
            'reu' =>    [
                'maps' =>   ['eu', 'as', 'af' ],
                'title' =>  'Maps for European Listeners'
            ],
            'rna' =>    [
                'maps' =>   [ 'na', 'alaska', 'sa', 'pacific', 'japan', 'polynesia' ],
                'title' =>  'Maps for North American Listeners'
            ],
            'rww' =>    [
                'maps' =>   [ 'na', 'sa', 'eu', 'as', 'af', 'au'],
                'title' =>  'Maps for All Listeners'
            ],
        ];
    }

    protected function getMapsForSystem($system)
    {
        return $this->getMapsForSystems()[$system]['maps'];
    }

    protected function getRegions()
    {
        return [
            'af' => ['title' => 'Africa',               'map' => 'map_af'],
            'an' => ['title' => 'Antarctica',           'map' => false   ],
            'as' => ['title' => 'Asia',                 'map' => 'map_as'],
            'ca' => ['title' => 'Carribean',            'map' => 'map_na'],
            'eu' => ['title' => 'Europe',               'map' => 'map_eu'],
            'iw' => ['title' => 'International Waters', 'map' => false   ],
            'na' => ['title' => 'North America',        'map' => 'map_na'],
            'oc' => ['title' => 'Oceania (Pacific)',    'map' => false   ],
            'sa' => ['title' => 'South America',        'map' => 'map_sa'],
            'xx' => ['title' => 'Unknown',              'map' => false   ]
        ];
    }

    protected function countRegionsWithMaps()
    {
        $count = 0;
        foreach ($this->getRegions() as $region => $config) {
            $count+= $config['map'] ? 1 : 0;
        }
        return $count;
    }

    /**
     * @return array
     */
    protected function getSystems()
    {
        return ['reu', 'rna', 'rww'];
    }

    /**
     * @return array
     */
    protected function getVisitors()
    {
        return [
            'CAN'   =>  ['IP' => '206.248.171.206', 'system' => 'rna'],     // Canada, Ontario
            'USA'   =>  ['IP' => '72.130.194.78',   'system' => 'rna'],     // USA, Minnesota
            'ENG'   =>  ['IP' => '213.219.36.56',   'system' => 'reu'],     // England, London
            'RUS'   =>  ['IP' => '95.31.18.119',    'system' => 'reu'],     // Russia, Moscow
            'AUS'   =>  ['IP' => '202.86.32.122',   'system' => 'rww'],     // Brisbane, Australia
        ];
    }

    /**
     * @return array
     */
    protected function getAdminUsers()
    {
        return [
            'bogus1' => [
                'password' =>   'password1',
                'valid' =>      false
            ],
            getenv('ADMIN_USER') => [
                'password' =>   getenv('ADMIN_PASS'),
                'valid' =>      true,
            ],
            'bogus2' => [
                'password' =>   'password2',
                'valid' =>      false
            ],
        ];
    }

    protected function getAdminUser()
    {
        return [
            'user' =>       getenv('ADMIN_USER'),
            'password' =>   getenv('ADMIN_PASS')
        ];
    }

    /**
     * @return mixed
     */
    protected function getMyCrawler()
    {
        return $this->myClient->getCrawler();
    }

    /**
     * @param $match
     * @return mixed
     */
    protected function filter($match)
    {
        return $this->getMyCrawler()->filter($match);
    }

    /**
     * @param null $id
     * @param array $args
     * @return string
     */
    protected function getError($id = null, $args = [])
    {
        return
            str_replace(
                "\n",
                "\n\x7f       ",
                sprintf("\x7f   ".static::MESSAGES[$id], ...$args)
            )
            ."\n";
    }

    /**
     * @return mixed
     */
    protected function getMyResponse()
    {
        return $this->myClient->getResponse();
    }

    /**
     * @return mixed
     */
    protected function getMyResponseContent()
    {
        return $this->getMyResponse()->getContent();
    }

    /**
     * @return mixed
     */
    protected function getMyResponseRedirectLocation()
    {
        return $this->getMyResponse()->headers->get('location');
    }

    /**
     * @return mixed
     */
    protected function getMyResponsePageTitle()
    {
        return $this->getMyCrawler()->filter('title')->eq(0)->text();
    }

    /**
     * @return mixed
     */
    protected function getMyResponseStatusCode()
    {
        return $this->getMyResponse()->getStatusCode();
    }

    /**
     * @return $this
     */
    protected function setNoRedirect()
    {
        $this->currentRedirectStatus = false;
        $this->myClient->setMaxRedirects(1);
        $this->myClient->followRedirects(false);
        return $this;
    }

    /**
     * @return $this
     */
    protected function setYesRedirect()
    {
        $this->currentRedirectStatus = true;
        $this->myClient->setMaxRedirects(10);
        $this->myClient->followRedirects(true);
        return $this;
    }

    protected function setUserAdmin()
    {
        $initialRedirectStatus = $this->currentRedirectStatus;
        $this->setYesRedirect();
        $admin = $this->getAdminUser();
        $this->myClient->request('GET', '/en/rww/admin/logon');
        $form = $this
            ->getMyCrawler()
            ->filter('button#form_submit')
            ->form(
                [
                    'form[user]' => $admin['user'],
                    'form[password]' => $admin['password']
                ],
                'POST'
            );
        $this->myClient->submit($form);
        if (!$initialRedirectStatus) {
            $this->setNoRedirect();
        }
        $this->currentUserType = 'admin';
    }

    protected function setUserPublic()
    {
        $this->myClient->request('GET', '/en/rww/admin/logoff');
        $this->currentUserType = 'public';
    }

    /**
     * @param $IP
     * @return $this
     */
    protected function setVisitorIP($IP)
    {
        putenv('PHPUNIT_CLIENT_IP='.$IP);
        return $this;
    }
}
