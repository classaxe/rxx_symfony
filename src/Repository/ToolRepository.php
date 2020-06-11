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
        'sunrise' => [
            'mode' =>           'Sunrise / Sunset Calculator',
            'shortName' =>      'Sunrise Calc',
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
        $out = [
            'tools' =>   [],
            'title' =>  'Tools'
        ];
        foreach (array_keys(static::TOOLS) as $key) {
            $out['tools'][$key] = static::TOOLS[$key];
        }

        return $out;
    }
}
