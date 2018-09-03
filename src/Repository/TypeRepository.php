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

    public function getTypeForCode($code)
    {
        switch ($code) {
            case 0:
                return ['class' => 'NDB',       'title' => 'NDB Beacon'];
            case 1:
                return ['class' => 'DGPS',      'title' => 'DGPS Station'];
            case 2:
                return ['class' => 'TIME',      'title' => 'Time Signal Station'];
            case 3:
                return ['class' => 'NAVTEX',    'title' => 'NavTex Station'];
            case 4:
                return ['class' => 'HAMBCN',    'title' => 'Amateur Radio Beacon'];
            case 5:
                return ['class' => 'OTHER',     'title' => 'Other form of transmission'];
            case 6:
                return ['class' => 'DSC',       'title' => 'DSC Station'];
            default:
                return ['class' => '',          'title' => ''];
        }
    }
}
