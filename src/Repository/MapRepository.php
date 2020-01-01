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
        ],
        'alaska' => [
            'mode' =>           'Beacons in Alaska',
            'map' =>            'map_alaska_beacons.gif',
            'text' =>           'OR... try the <a href="state_map/?simple=1&SP=AK">interactive map of Alaska</a>',
            'shortName' =>      'Alaska',
        ],
        'as' => [
            'mode' =>           'Asian NDB List Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'map' =>            'as_map.gif',
            'shortName' =>      'Asia',
        ],
        'au' => [
            'mode' =>           'Australian NDB List Country Codes',
            'stateBtn' =>       'Territories',
            'stateFilter' =>    'aus',
            'map' =>            'au_map.gif',
            'shortName' =>      'Australia',
        ],
        'eu' => [
            'mode' =>           'European NDB List Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'eu',
            'map' =>            'eu_map.gif',
            'shortName' =>      'Europe',
        ],
        'japan' => [
            'mode' =>           'Japanese NDB List Country Codes',
            'map' =>            'japan_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'shortName' =>      'Japan',
        ],
        'na' => [
            'mode' =>           'North American NDB List Country Codes',
            'stateBtn' =>       'States',
            'stateFilter' =>    'can,usa',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'na',
            'map' =>            'na_map.gif',
            'shortName' =>      'North America',
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
        ],
        'polynesia' => [
            'mode' =>           'French Polynesian Beacons Map',
            'map' =>            'map_french_polynesia.gif',
            'shortName' =>      'French Polynesia',
        ],
        'sa' => [
            'mode' =>           'South American NDB List Country Codes',
            'map' =>            'sa_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'sa',
            'shortName' =>      'South America',
        ]
    ];

    const SYSTEM_MAPS = [
        'reu' =>    [
            'maps' =>   ['eu', 'af', 'as' ],
            'title' =>  'Maps for European Listeners'
        ],
        'rna' =>    [
            'maps' =>   [ 'na', 'alaska', 'sa', 'pacific', 'japan', 'polynesia' ],
            'title' =>  'Maps for North American Listeners'
        ],
        'rww' =>    [
            'maps' =>   [ 'af', 'as', 'au', 'eu', 'na', 'sa' ],
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
