<?php

namespace App\Repository;

class SystemRepository
{
    const AUTHORS = [
        [
            'email' =>      'martin@classaxe.com',
            'name' =>       'Martin Francis',
            'role' =>       'Software Development',
            'show_email' => true
        ],
        [
            'email' =>      'kb8qgf@gmail.com',
            'name' =>       'Andy Robins',
            'role' =>       'Initial Concept',
            'show_email' => false
        ],
    ];

    const AWARDS = [
        [
            'email' =>      'kj8o.ham@gmail.com',
            'name' =>       'Joseph Miller, KJ8O',
            'role' =>       'Awards Coordinator',
            'show_email' => false
        ]
    ];

    const SYSTEMS = [
        'rna' =>    [
            'authors' =>    self::AUTHORS,
            'awards' =>     self::AWARDS,
            'editors' =>    [
                [
                    'email' =>      'peterconway@talktalk.net',
                    'name' =>       'Peter Conway',
                    'role' =>       'DSC Signals',
                    'show_email' => true
                ],
                [
                    'email' =>      'smoketronics@comcast.net',
                    'name' =>       'S M O\'Kelley',
                    'role' =>       'NDBs and Ham Beacons',
                    'show_email' => true
                ],
                [
                    'email' =>      'roelof@ndb.demon.nl',
                    'name' =>       'Roelof Bakker',
                    'role' =>       'DGPS and Navtex',
                    'show_email' => true
                ],
            ],
            'menu' =>       'North America (RNA)',
            'title' =>      'Signals Received in N & C America + Hawaii',
        ],
        'reu' =>    [
            'authors' =>    self::AUTHORS,
            'awards' =>     self::AWARDS,
            'editors' =>    [
                [
                    'email' =>      'aunumero73@gmail.com',
                    'name' =>       'Pat Vignoud',
                    'role' =>       'NDBs',
                    'show_email' => true
                ],
                [
                    'email' =>      'peterconway@talktalk.net',
                    'name' =>       'Peter Conway',
                    'role' =>       'DSC Signals',
                    'show_email' => true
                ],
                [
                    'email' =>      'smoketronics@comcast.net',
                    'name' =>       'S M O\'Kelley',
                    'role' =>       'Ham Beacons',
                    'show_email' => true
                ],
                [
                    'email' =>      'roelof@ndb.demon.nl',
                    'name' =>       'Roelof Bakker',
                    'role' =>       'DGPS and Navtex',
                    'show_email' => true
                ],
            ],
            'menu' =>       'Europe (REU)',
            'title' =>      'Signals Received in Europe',
        ],
        'rww' =>    [
            'authors' =>    self::AUTHORS,
            'awards' =>     self::AWARDS,
            'editors' =>    [
                [
                    'email' =>      'peterconway@talktalk.net',
                    'name' =>       'Peter Conway',
                    'role' =>       'DSC Signals',
                    'show_email' => true
                ],
                [
                    'email' =>      'smoketronics@comcast.net',
                    'name' =>       'S M O\'Kelley',
                    'role' =>       'NDBs and Ham Beacons',
                    'show_email' => true
                ],
                [
                    'email' =>      'roelof@ndb.demon.nl',
                    'name' =>       'Roelof Bakker',
                    'role' =>       'DGPS and Navtex',
                    'show_email' => true
                ],
            ],
            'menu' =>       'Worldwide (RWW)',
            'title' =>      'Signals Received Worldwide',
        ]
    ];

    public function getClassicUrl($system='', $mode='')
    {
        $base = "https://www.classaxe.com/dx/ndb/$system/";
        switch ($mode) {
            case 'cle':
                return $base . 'cle';
            case 'help':
                return $base . 'help';
            case 'listeners':
                return $base . 'listener_list';
            case 'logon':
                return $base . 'logon';
            case 'maps':
                return $base . 'maps';
            case 'signals':
                return $base . 'signal_list';
        }
        return $base;
    }

    public function get($code)
    {
        return self::SYSTEMS[$code];
    }

    public function getAll()
    {
        return self::SYSTEMS;
    }
}
