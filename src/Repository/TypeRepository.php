<?php

namespace App\Repository;

class TypeRepository
{
    public function getAll()
    {
        return [
            'DGPS' =>   'type_DGPS',
            'DSC' =>    'type_DSC',
            'Ham' =>    'type_HAMBCN',
            'Navtex' => 'type_NAVTEX',
            'NDB' =>    'type_NDB',
            'Time' =>   'type_TIME',
            'Other' =>  'type_OTHER',
            '(All)' =>  'type_ALL'
        ];
    }

    public function getColorsForCodes()
    {
        $out = [];
        for($i=0; $i<=6; $i++) {
            $out[$i] = static::getTypeForCode($i)['color'];
        }
        return $out;
    }

    public function getMapIconColorForCodes()
    {
        // Format is bbggrr - alpha, blue, green, red NOT rrggbb as more usually seen
        $out = static::getColorsForCodes();
        foreach ($out as $key => &$value) {
            $value =
                 substr($value, 4, 2)
                .substr($value, 2, 2)
                .substr($value, 0, 2);
        }
        return $out;
    }

    public function getTypeForCode($code)
    {
        switch ($code) {
            case 0:
                return ['class' => 'NDB',       'color' => 'ffffff',    'title' => 'NDB Beacon'];
            case 1:
                return ['class' => 'DGPS',      'color' => '00d8ff',    'title' => 'DGPS Station'];
            case 2:
                return ['class' => 'TIME',      'color' => 'ffe0b0',    'title' => 'Time Signal Station'];
            case 3:
                return ['class' => 'NAVTEX',    'color' => 'ffb8d8',    'title' => 'NavTex Station'];
            case 4:
                return ['class' => 'HAMBCN',    'color' => 'b8ffc0',    'title' => 'Amateur Radio Beacon'];
            case 5:
                return ['class' => 'OTHER',     'color' => 'b8f8ff',    'title' => 'Other form of transmission'];
            case 6:
                return ['class' => 'DSC',       'color' => 'ffb000',    'title' => 'DSC Station'];
            default:
                return ['class' => '',          'title' => ''];
        }
    }
}
