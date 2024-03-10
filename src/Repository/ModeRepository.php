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
                'menu' =>   'Locations',
                'title' =>  'Listener Locations',
                'url'=>     false
            ],
            'logsessions' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Log Sessions',
                'title' =>  'Log Sessions',
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
            'donate' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Donate',
                'title' =>  'Donate',
                'url'=>     false
            ],
        ],
        [
            'ndblistWebsite' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Ndblist.info',
                'title' =>  'NDB List Group Information Website',
                'url'=>     'http://ndblist.info/'
            ],
            'NdbGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'ndblist',
                'title' =>  'NDB at Groups.io',
                'url'=>     'https://groups.io/g/ndblist'
            ],
            'DscGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'dsc-list',
                'title' =>  'DSC at Groups.io',
                'url'=>     'https://groups.io/g/dsc-list'
            ],
            'DgpsGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'dgpslist',
                'title' =>  'DGPS at Groups.io',
                'url'=>     'https://groups.io/g/dgpslist'
            ],
            'NavtexGroup' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'navtexdx',
                'title' =>  'Navtex at Groups.io',
                'url'=>     'https://groups.io/g/navtexdx'
            ],
            'changes' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Changes',
                'title' =>  'Changes',
                'url'=>     false
            ],
            'logon' => [
                'access' => User::PUBLIC,
                'admin' =>  false,
                'guest' =>  true,
                'menu' =>   'Logon',
                'title' =>  'Logon',
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
            'help' => [
                'access' => User::ALL,
                'admin' =>  true,
                'guest' =>  true,
                'menu' =>   'Help',
                'title' =>  'Help',
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
                'access' => User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Info',
                'title' =>  'System Info',
                'url'=>     false
            ],
            'users' => [
                'access' => User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Users',
                'title' =>  'User Accounts',
                'url'=>     false
            ],
            'donors' => [
                'access' => User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Donors',
                'title' =>  'People who have donated',
                'url'=>     false
            ],
            'donations' => [
                'access' => User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Gifts',
                'title' =>  'Gifts received',
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
            'clePlanner' => [
                'access' => User::CLE | User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'CLE Planner',
                'title' =>  'View loggings for various ranges',
                'url'=>     false
            ],
            'profile' => [
                'access' => User::USER | User::CLE | User::AWARDS | User::ADMIN | User::MASTER,
                'admin' =>  true,
                'guest' =>  false,
                'menu' =>   'Your Profile',
                'title' =>  'Edit your profile',
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
