<?php

namespace App\Repository;


class ModeRepository
{
    const modes = [
        'signal_list' =>    [
            'menu' =>   'Signals',
            'title' =>  'Signals List'
        ],
        'maps' =>    [
            'menu' =>   'Maps',
            'title' =>  'Maps'
        ],
        'listener_list' =>    [
            'menu' =>   'Listeners',
            'title' =>  'Listeners List'
        ],

    ];

    public function getAll()
    {
        return self::modes;
    }

    public function get($code)
    {
        return self::modes[$code];
    }
}
