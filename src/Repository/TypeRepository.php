<?php

namespace App\Repository;

class TypeRepository
{
    const types = [
        1 =>    ['bbggrr' => 'ffd800',  'order' =>  0,  'class' => 'DGPS',      'color' => '00d8ff',    'label' => 'DGPS',     'title' => 'DGPS Station'],
        6 =>    ['bbggrr' => '00b0ff',  'order' =>  1,  'class' => 'DSC',       'color' => 'ffb000',    'label' => 'DSC',      'title' => 'DSC Station'],
        4 =>    ['bbggrr' => 'c0ffb8',  'order' =>  2,  'class' => 'HAMBCN',    'color' => 'b8ffc0',    'label' => 'Ham',      'title' => 'Amateur Radio Beacon'],
        3 =>    ['bbggrr' => 'd8b8ff',  'order' =>  3,  'class' => 'NAVTEX',    'color' => 'ffb8d8',    'label' => 'Navtex',   'title' => 'NavTex Station'],
        0 =>    ['bbggrr' => 'ffffff',  'order' =>  4,  'class' => 'NDB',       'color' => 'ffffff',    'label' => 'NDB',      'title' => 'NDB Beacon'],
        2 =>    ['bbggrr' => 'b0e0ff',  'order' =>  5,  'class' => 'TIME',      'color' => 'ffe0b0',    'label' => 'Time',     'title' => 'Time Signal Station'],
        5 =>    ['bbggrr' => 'fff8b8',  'order' =>  6,  'class' => 'OTHER',     'color' => 'b8f8ff',    'label' => 'Other',    'title' => 'Other form of transmission'],
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

    public function getSignalTypesSearched(array $types)
    {
        $out = [];
        foreach (static::types as $key=>$type) {
            if (in_array('type_'.$type['class'], $types)){
                $out[] = $key;
            }
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

    public function sortByOrder($a, $b) {
        if ($a['order'] == $b['order']) {
            return 0;
        }
        return ($a['order'] < $b['order']) ? -1 : 1;
    }
}
