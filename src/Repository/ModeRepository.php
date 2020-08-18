<?php

namespace App\Repository;
use App\Entity\User;

class ModeRepository
{

    const MODES = [
        [
            'signals' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Signals',
                'title' =>  'Signals List',
                'url'=>     false
            ],
            'listeners' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Listeners',
                'title' =>  'Listeners List',
                'url'=>     false
            ],
            'cle' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'CLE',
                'title' =>  'CLE',
                'url'=>     false
            ],
            'maps' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Maps',
                'title' =>  'Maps',
                'url'=>     false
            ],
            'tools' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Tools',
                'title' =>  'Tools',
                'url'=>     false
            ],
            'weather' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Weather',
                'title' =>  'Weather',
                'url'=>     false
            ],
            'changes' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Changes',
                'title' =>  'Changes',
                'url'=>     false
            ],
            'help' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Help',
                'title' =>  'Help',
                'url'=>     false
            ],
/*
            'donate' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Donate',
                'title' =>  'Donate',
                'url'=>     false
            ],
*/
        ],
        [
            'ndblistWebsite' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'NDB List Website',
                'title' =>  'NDB List Group Information Website',
                'url'=>     'http://ndblist.info/'
            ],
            'NdbGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'NDB Group',
                'title' =>  'NDB at Groups.io',
                'url'=>     'https://groups.io/g/ndblist'
            ],
            'DscGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'DSC Group',
                'title' =>  'DSC at Groups.io',
                'url'=>     'https://groups.io/g/dsc-list'
            ],
            'DgpsGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'DGPS Group',
                'title' =>  'DGPS at Groups.io',
                'url'=>     'https://groups.io/g/dgpslist'
            ],
            'NavtexGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'NAVTEX Group',
                'title' =>  'Navtex at Groups.io',
                'url'=>     'https://groups.io/g/navtexdx'
            ],
            'logon' => [
                'access' => User::PUBLIC,
                'admin' =>  false,
                'guest' =>  true,
                'menu' =>   'Log On',
                'title' =>  'Logon',
                'url'=>     false
            ],
        ],
        [
            'admin/tools' => [
                'access' => User::ADMIN | User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Admin Tools',
                'title' =>  'Tools to help Admins Manage this system',
                'url'=>     false
            ],
            'admin/info' => [
                'access' => User::ADMIN | User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'System Info',
                'title' =>  'System Info',
                'url'=>     false
            ],
            'admin/users' => [
                'access' => User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'User Accounts',
                'title' =>  'User Accounts',
                'url'=>     false
            ],
            'admin/help' => [
                'access' => User::AWARDS | User::ADMIN | User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Admin Help',
                'title' =>  'Admin Help',
                'url'=>     false
            ],
            'admin/profile' => [
                'access' => User::USER | User::CLE | User::AWARDS | User::ADMIN | User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Your Profile',
                'title' =>  'Edit your profile',
                'url'=>     false
            ],
            'logoff' => [
                'access' => User::USER | User::CLE | User::AWARDS | User::ADMIN | User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Logoff',
                'title' =>  'Log Off',
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
