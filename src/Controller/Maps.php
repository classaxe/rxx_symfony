<?php
namespace App\Controller;

use App\Service\Region as RegionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use App\Utils\Rxx;

/**
 * Class CountryLocator
 * @package App\Controller
 */
class Maps extends Controller {

    private $parameters = [
        'af' => [
            'mode' =>           'African NDB List approved Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'af',
            'map' =>            'af_map.gif',
            'shortName' =>      'Africa'
        ],
        'alaska' => [
            'mode' =>           'Beacons in Alaska',
            'map' =>            'map_alaska_beacons.gif',
            'text' =>           'OR... try the <a href="state_map/?simple=1&SP=AK">interactive map of Alaska</a>',
            'shortName' =>      'Alaska'
        ],
        'as' => [
            'mode' =>           'Asian NDB List approved Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'map' =>            'as_map.gif',
            'shortName' =>      'Asia'
        ],
        'au' => [
            'mode' =>           'Australian NDB List approved Country Codes',
            'stateBtn' =>       'Territories',
            'stateFilter' =>    'aus',
            'map' =>            'au_map.gif',
            'shortName' =>      'Australia'
        ],
        'eu' => [
            'mode' =>           'European NDB List approved Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'eu',
            'map' =>            'eu_map.gif',
            'shortName' =>      'Europe'
        ],
        'japan' => [
            'mode' =>           'Japanese NDB List approved Country Codes',
            'map' =>            'japan_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'shortName' =>      'Japan'
        ],
        'na' => [
            'mode' =>           'North + Central American NDB List approved Country Codes',
            'stateBtn' =>       'States',
            'stateFilter' =>    'can,usa',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'na',
            'map' =>            'na_map.gif',
            'shortName' =>      'North America'
        ],
        'pacific' => [
            'mode' =>           'Pacific Beacons Map',
            'map' =>            'pacific_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'oc',
            'text' =>           '(Originally produced for <a href="/dx/ndb/log/steve/?mode=station_list&yyyymm=200307">Steve Ratzlaff\'s Pacific Report</a>)',
            'shortName' =>      'Pacific'
        ],
        'polynesia' => [
            'mode' =>           'French Polynesian Beacons Map',
            'map' =>            'map_french_polynesia.gif',
            'shortName' =>      'French Polynesia'
        ],
        'sa' => [
            'mode' =>           'South American NDB List approved Country Codes',
            'map' =>            'sa_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'sa',
            'shortName' =>      'South America'
        ]
    ];

    private $system_zones = [
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

    /**
     * @Route(
     *     "/{system}/map_{area}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "area": "af|alaska|as|au|eu|japan|na|pacific|polynesia|sa"
     *     },
     *     name="show_map"
     * )
     */
    public function map($system,$area)
    {
        $parameters = $this->parameters[$area];
        $parameters['system'] = $system;

        return $this->render('maps/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/maps",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="show_maps"
     * )
     */
    public function maps($system)
    {
        $parameters = [
            'zones' =>   [],
            'mode' =>   'Maps',
            'system' => $system,
            'title' =>  $this->system_zones[$system]['title']
        ];
        foreach($this->system_zones[$system]['maps'] as $zone) {
            $parameters['zones'][$zone] = $this->parameters[$zone];
        }

//        return Rxx::debug($parameters);
        return $this->render('maps/index.html.twig', $parameters);
    }
}