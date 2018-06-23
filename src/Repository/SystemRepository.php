<?php

namespace App\Repository;


class SystemRepository
{
    const systems = [
        'rna' =>    [
            'menu' =>   'North America (RNA)',
            'title' =>  'Signals Received in N & C America + Hawaii'
        ],
        'reu' =>    [
            'menu' =>   'Europe (REU)',
            'title' =>  'Signals Received in Europe'
        ],
        'rww' =>    [
            'menu' =>   'Worldwide (RWW)',
            'title' =>  'Signals Received Worldwide'
        ]
    ];

    public function getAll()
    {
        return self::systems;
    }

    public function get($code)
    {
        return self::systems[$code];
    }
}
