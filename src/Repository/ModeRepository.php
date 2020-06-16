<?php

namespace App\Repository;

class ModeRepository
{
    const MODES = [
        [
            'signals' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Signals',
                'title' =>  'Signals List',
                'url'=>     false
            ],
            'listeners' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Listeners',
                'title' =>  'Listeners List',
                'url'=>     false
            ],
            'cle' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'CLE',
                'title' =>  'CLE',
                'url'=>     false
            ],
            'maps' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Maps',
                'title' =>  'Maps',
                'url'=>     false
            ],
            'tools' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Tools',
                'title' =>  'Tools',
                'url'=>     false
            ],
            'weather' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Weather',
                'title' =>  'Weather',
                'url'=>     false
            ],
            'changes' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Changes',
                'title' =>  'Changes',
                'url'=>     false
            ],
            'help' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Help',
                'title' =>  'Help',
                'url'=>     false
            ],
            'donate' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Donate',
                'title' =>  'Donate',
                'url'=>     false
            ],
        ],
        [
            'ndblistWebsite' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'NDB List Website',
                'title' =>  'NDB List Group Information Website',
                'url'=>     'http://ndblist.info/'
            ],
            'NdbGroup' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'NDB Group',
                'title' =>  'NDB at Groups.io',
                'url'=>     'https://groups.io/g/ndblist'
            ],
            'DscGroup' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'DSC Group',
                'title' =>  'DSC at Groups.io',
                'url'=>     'https://groups.io/g/dsc-list'
            ],
            'DgpsGroup' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'DGPS Group',
                'title' =>  'DGPS at Groups.io',
                'url'=>     'https://groups.io/g/dgpslist'
            ],
            'NavtexGroup' => [
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'NAVTEX Group',
                'title' =>  'Navtex at Groups.io',
                'url'=>     'https://groups.io/g/navtexdx'
            ],
            'logon' => [
                'admin' =>  false,
                'guest' =>  true,
                'menu' =>   'Log On',
                'title' =>  'Logon',
                'url'=>     false
            ],
            'logoff' => [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Log Off',
                'title' =>  'Log Off',
                'url'=>     false
            ],
        ],
        [
            'admin/tools' => [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Admin Tools',
                'title' =>  'Tools to help Admins Manage this system',
                'url'=>     false
            ],
            'admin/info' => [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'System Info',
                'title' =>  'System Info',
                'url'=>     false
            ],
            'admin/help' => [
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Admin Help',
                'title' =>  'Admin Help',
                'url'=>     false
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
