<?php

namespace App\Repository;

class ToolRepository
{
    const TOOLS = [
        'dgps' => [
            'mode' =>           'DGPS Station ID Lookup',
            'shortName' =>      'DGPS Lookup',
        ],
        'coordinates' => [
            'mode' =>           'Coordinates Converter',
            'shortName' =>      'Coordinates',
        ],
        'navtex' => [
            'mode' =>           'NAVTEX Missed Shift Fixer (v1.4)',
            'shortName' =>      'NAVTEX Fixer',
        ],
        'negativeKeyer' => [
            'mode' =>           'Negative Keying Tool (v7.0, &copy;Brian Keyte + J. Rabe)',
            'shortName' =>      'Negative Keying Tool',
        ],
        'sunrise' => [
            'mode' =>           'Sunrise / Sunset Calculator',
            'shortName' =>      'Sunrise Calc',
        ],
        'chirpconvert' => [
            'mode' =>           'CHIRP FT-70D to Yaesu FTM-200D csv converter',
            'shortName' =>      'Chirp Convert',
        ],
        'references' => [
            'mode' =>           'Other Database References on the Web',
            'shortName' =>      'Other References',
        ],
    ];

    public static function get($key)
    {
        return static::TOOLS[$key];
    }

    public static function getAll()
    {
        return static::TOOLS;
    }
}
