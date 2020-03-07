<?php

namespace App\Repository;

class AwardRepository
{
    const AWARDSPEC = [
        'continental_eu' => [ 10, 20, 30, 40 ],
        'continental_na' => [ 10, 30, 45, 60 ],
        'continental_ca' => [  3, 10, 15, 20 ],
        'continental_sa' => [  1,  3,  5, 10 ],
        'continental_af' => [  1,  5, 10, 15 ],
        'continental_as' => [  1,  3,  5, 10 ],
        'continental_oc' => [  1,  3,  5, 10 ],
        'continental_an' => [  1 ],
        'country_benelux' => [
            'ALL' => true,
            'ITU' => [ 'BEL', 'HOL', 'LUX' ],
            'QTY' => [  5, 15, 25, 35 ]
        ],
        'country_france' => [
            'ALL' => false,
            'ITU' => [ 'FRA', 'COR' ],
            'QTY' => [ 20, 40, 80, 120 ]
        ],
        'country_germany' => [
            'ALL' => false,
            'ITU' => [ 'DEU' ],
            'QTY' => [ 20, 30, 60, 100 ]
        ],
        'country_scandanavia' => [
            'ALL' => false,
            'ITU' => [ 'SWE', 'NOR', 'FIN', 'DNK' ],
            'QTY' => [ 25, 100, 200, 300 ]
        ],
        'country_uk' => [
            'ALL' => false,
            'ITU' => [ 'ENG', 'WLS', 'IOM', 'GSY', 'JSY', 'SCT', 'ORK', 'SHE', 'NIR', 'IRL' ],
            'QTY' => [ 20, 40, 80, 120 ]
        ],
        'country_italy' => [
            'ALL' => false,
            'ITU' => [ 'ITA', 'SAR', 'SCY' ],
            'QTY' => [ 20, 40, 60, 90 ]
        ],
        'country_iberia' =>  [
            'ALL' => false,
            'ITU' => [ 'ESP', 'POR', 'BAL' ],
            'QTY' => [ 20, 40, 60, 90 ]
        ],
        'daytime' => [
            [ 250,   499,  402,  804 ],
            [ 500,   749,  805, 1205 ],
            [ 750,   999, 1206, 1607 ],
            [ 1000, 1249, 1608, 2010 ],
            [ 1250,    0, 1608,    0 ]
        ],
        'north60' => [  5, 10, 20, 30 ],
        'longranger' => [
            [ 500,   999,  805, 1607 ],
            [ 1000, 1499, 1608, 2413 ],
            [ 1500, 1999, 2414, 3217 ],
            [ 2000, 2499, 3218, 4022 ],
            [ 2500, 2999, 4023, 4826 ],
            [ 3000, 4999, 4827, 8044 ],
            [ 5000,    0, 8045,    0 ]
        ],
        'lt' => [ 'call' => 'LT', 'khz' => 305 ],
        'transatlantic' => [
            'eu' => [
                'CON' => [ 'ca', 'na', 'sa' ],
                'QTY' => [ 1, 10 ]
            ],
            'na' => [
                'CON' => [ 'af', 'eu' ],
                'QTY' => [ 1, 3 ]
            ]
        ],
        'transpacific' => [
            'ca,na,sa' => [
                'LOC' => [ 'as',  'AUS', 'NZL', 'PNG' ],
                'QTY' => [ 1, 2, 3, 4 ]
            ],
            'as,AUS,NZL,PNG' => [
                'LOC' => [ 'ca', 'na', 'sa' ],
                'QTY' => [ 1, 2, 3, 4 ]
            ]
        ]
    ];

    /**
     * @param $type
     * @return mixed
     */
    public function getAwardSpec($type)
    {
        return static::AWARDSPEC[$type];
    }
}
