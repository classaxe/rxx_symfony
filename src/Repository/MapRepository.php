<?php

namespace App\Repository;

class MapRepository
{
    const MAPS = [
        'af' => [
            'mode' =>           'African NDB List approved Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'af',
            'map' =>            'af_map.gif',
            'shortName' =>      'Africa',
            'rel' =>            'popup|map_af|width=646,height=652,resizable=1',
        ],
        'alaska' => [
            'mode' =>           'Beacons in Alaska',
            'map' =>            'map_alaska_beacons.gif',
            'text' =>           'OR... try the <a href="state_map/?simple=1&SP=AK">interactive map of Alaska</a>',
            'shortName' =>      'Alaska',
            'rel' =>            'popup|map_alaska|width=466,height=443,resizable=1',
        ],
        'as' => [
            'mode' =>           'Asian NDB List approved Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'map' =>            'as_map.gif',
            'shortName' =>      'Asia',
            'rel' =>            'popup|map_as|width=856,height=575,resizable=1',
        ],
        'au' => [
            'mode' =>           'Australian NDB List approved Country Codes',
            'stateBtn' =>       'Territories',
            'stateFilter' =>    'aus',
            'map' =>            'au_map.gif',
            'shortName' =>      'Australia',
            'rel' =>            'popup|map_au|width=511,height=469,resizable=1',
        ],
        'eu' => [
            'mode' =>           'European NDB List approved Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'eu',
            'map' =>            'eu_map.gif',
            'shortName' =>      'Europe',
            'rel' =>            'popup|map_eu|width=704,height=696,resizable=1',
        ],
        'japan' => [
            'mode' =>           'Japanese NDB List approved Country Codes',
            'map' =>            'japan_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'shortName' =>      'Japan',
            'rel' =>            'popup|map_japan|width=517,height=690,resizable=1',
        ],
        'na' => [
            'mode' =>           'North + Central American NDB List approved Country Codes',
            'stateBtn' =>       'States',
            'stateFilter' =>    'can,usa',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'na',
            'map' =>            'na_map.gif',
            'shortName' =>      'North America',
            'rel' =>            'popup|map_na|width=669,height=660,resizable=1',
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
            'rel' =>            'popup|map_pacific|width=366,height=429,resizable=1',
        ],
        'polynesia' => [
            'mode' =>           'French Polynesian Beacons Map',
            'map' =>            'map_french_polynesia.gif',
            'shortName' =>      'French Polynesia',
            'rel' =>            'popup|map_polynesia|width=458,height=440,resizable=1',
        ],
        'sa' => [
            'mode' =>           'South American NDB List approved Country Codes',
            'map' =>            'sa_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'sa',
            'shortName' =>      'South America',
            'rel' =>            'popup|map_sa|width=490,height=686,resizable=1',
        ]
    ];

    const SYSTEM_MAPS = [
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

    public static function get($key)
    {
        return static::MAPS[$key];
    }

    public static function getAllForSystem($system)
    {
        $out = [
            'maps' =>   [],
            'title' =>  static::SYSTEM_MAPS[$system]['title']
        ];

        foreach (static::SYSTEM_MAPS[$system]['maps'] as $zone) {
            $out['maps'][$zone] = static::MAPS[$zone];
        }

        return $out;
    }
}
