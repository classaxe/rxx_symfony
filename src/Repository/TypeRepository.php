<?php

namespace App\Repository;

class TypeRepository
{
    const types = [
        0 =>    ['bbggrr' => 'ffffff',  'class' => 'NDB',       'color' => 'ffffff',    'label' => 'NDB',      'title' => 'NDB Beacon'],
        1 =>    ['bbggrr' => 'ffd800',  'class' => 'DGPS',      'color' => '00d8ff',    'label' => 'DGPS',     'title' => 'DGPS Station'],
        2 =>    ['bbggrr' => 'b0e0ff',  'class' => 'TIME',      'color' => 'ffe0b0',    'label' => 'Time',     'title' => 'Time Signal Station'],
        3 =>    ['bbggrr' => 'd8b8ff',  'class' => 'NAVTEX',    'color' => 'ffb8d8',    'label' => 'Navtex',   'title' => 'NavTex Station'],
        4 =>    ['bbggrr' => 'c0ffb8',  'class' => 'HAMBCN',    'color' => 'b8ffc0',    'label' => 'Ham',      'title' => 'Amateur Radio Beacon'],
        5 =>    ['bbggrr' => 'fff8b8',  'class' => 'OTHER',     'color' => 'b8f8ff',    'label' => 'Other',    'title' => 'Other form of transmission'],
        6 =>    ['bbggrr' => '00b0ff',  'class' => 'DSC',       'color' => 'ffb000',    'label' => 'DSC',      'title' => 'DSC Station']
    ];

    public function getAll()
    {
        return static::types;
    }

    public function getAllChoices()
    {
        $out = [];
        foreach (static::types as $key => $type) {
            $out[$type['label']] = 'type_'.$type['class'];
        }
        ksort($out);
        $out['(All)'] = 'type_ALL';
        return $out;
    }

    public function getColorsForCodes()
    {
        $out = [];
        foreach (static::types as $key=>$type) {
            $out[$key] = $type['color'];
        }
        return $out;
    }

    public function getMapIconColorForCodes()
    {
        $out = [];
        foreach (static::types as $key=>$type) {
            $out[$key] = $type['bbggrr'];
        }
        return $out;
    }

    public function getTypeForCode($code)
    {
        return static::types[$code];
    }
}
