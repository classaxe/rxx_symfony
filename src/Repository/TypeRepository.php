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
}
