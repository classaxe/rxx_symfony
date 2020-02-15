<?php

namespace App\Repository;

class ModeRepository
{
    const MODES = [
        [
            'signals' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Signals',
                'title' =>  'Signals List'
            ],
            'listeners' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Listeners',
                'title' =>  'Listeners List'
            ],
            'cle' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'CLE',
                'title' =>  'CLE'
            ],
            'maps' =>    [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Maps',
                'title' =>  'Maps'
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
            'admin/help' =>    [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Admin Help',
                'title' =>  'Admin Help'
            ],
            'admin/info' =>    [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'System Info',
                'title' =>  'System Info'
            ],
        ]
    ];

    public function get($code)
    {
        return self::MODES[$code];
    }

    public function getAll()
    {
        return self::MODES;
    }
}
