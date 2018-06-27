<?php

namespace App\Repository;


class ModeRepository
{
    const modes = [
        [
            'signal_list' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Signals',
                'title' =>  'Signals List'
            ],
            'maps' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Maps',
                'title' =>  'Maps'
            ],
            'listener_list' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Listeners',
                'title' =>  'Listeners List'
            ],
            'logon' =>    [
                'admin' =>  false,
                'guest' =>  true,
                'menu' =>   'Log On',
                'title' =>  'Logon'
            ],
            'logoff' =>    [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Log Off',
                'title' =>  'Log Off'
            ],
            'help' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Help',
                'title' =>  'Help'
            ],
        ],
        [
            'signal_list' =>    [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Admin Help',
                'title' =>  'Admin Help'
            ],
        ]
    ];

    public function get($code)
    {
        return self::modes[$code];
    }

    public function getAll()
    {
        return self::modes;
    }

}
