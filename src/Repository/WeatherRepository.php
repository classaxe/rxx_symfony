<?php

namespace App\Repository;

class WeatherRepository
{
    const WIDGETS = [
        'aurora_n' => [
            'mode' =>           'Northern Solar Activity Chart',
            'shortName' =>      'Northern Aurora',
            'class' =>          'halfwidth'
        ],
        'aurora_s' => [
            'mode' =>           'Southern Solar Activity Chart',
            'shortName' =>      'Southern Aurora',
            'class' =>          'halfwidth'
        ],
        'lightning' => [
            'mode' =>           'Realtime Lightning Map',
            'shortName' =>      'Lightning',
            'class' =>          'fullwidth'
        ],
    ];

    const CENTERS = [
        'na' => [
            'label' =>          'N.America',
            'lat' =>            48.1667,
            'lon' =>            -100.1667,
            'zoom' =>           3
        ],
        'aa' => [
            'label' =>          'C America',
            'lat' =>            12.7690,
            'lon' =>            -85.6024,
            'zoom' =>           3
        ],
        'sa' => [
            'label' =>          'S America',
            'lat' =>            -15.6006,
            'lon' =>            -56.1004,
            'zoom' =>           3
        ],
        'eu' => [
            'label' =>          'Europe',
            'lat' =>            54.9,
            'lon' =>            25.3167,
            'zoom' =>           3
        ],
        'af' => [
            'label' =>          'Africa',
            'lat' =>            -8.7832,
            'lon' =>            34.5085,
            'zoom' =>           2
        ],
        'as' => [
            'label' =>          'Asia',
            'lat' =>            43.6769,
            'lon' =>            87.3311,
            'zoom' =>           2
        ],
        'au' => [
            'label' =>          'Australia',
            'lat' =>            -25.2744,
            'lon' =>            133.7751,
            'zoom' =>           3
        ],
        'oc' => [
            'label' =>          'Pacific',
            'lat' =>            -8.7832,
            'lon' =>            124.5085,
            'zoom' =>           1
        ],
    ];

    public static function get($key)
    {
        return static::WIDGETS[$key];
    }

    public static function getAll()
    {
        return static::WIDGETS;
    }

    public static function getCenter($key)
    {
        return static::CENTERS[$key];
    }

    public static function getCenters()
    {
        return static::CENTERS;
    }
}
