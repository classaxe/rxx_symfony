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

    public static function get($key)
    {
        return static::WIDGETS[$key];
    }

    public static function getAll()
    {
        return static::WIDGETS;
    }
}
