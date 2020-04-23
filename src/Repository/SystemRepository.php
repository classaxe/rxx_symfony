<?php

namespace App\Repository;
use Doctrine\DBAL\Driver\Connection;

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
            'name' =>       'Joseph Miller - KJ80',
            'role' =>       'Awards Coordinator',
            'show_email' => false
        ],
        [
            'email' =>      'ndbcle@gmail.com',
            'name' =>       'Brian Keyte, Joachim Rabe',
            'role' =>       'CLE Coordinators',
            'show_email' => true
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
            'menu_s' =>     'RNA',
            'title' =>      'Signals Received in N & C America + Hawaii',
            'title_s' =>    'Received in N America'
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
            'menu_s' =>     'REU',
            'title' =>      'Signals Received in Europe',
            'title_s' =>    'Received in Europe',
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
            'menu_s' =>     'RWW',
            'title' =>      'Signals Received Worldwide',
            'title_s' =>    'Received Worldwide',
        ]
    ];

    private $connection;
    /**
     * SystemRepository constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getClassicUrl($mode='', $submode='')
    {
        $response = [ 'type' => 'url', 'value' => '' ];
        switch ($mode) {
            case 'admin/help':
                $response['value'] = 'admin_help';
                break;
            case 'admin/info':
                $response['value'] = 'sys_info';
                break;
            case 'admin/tools':
                $response['value'] = 'admin_manage';
                break;
            case 'cle':
            case 'help':
            case 'logon':
            case 'maps':
                $response['value'] = $mode;
                break;
            case 'listeners':
                $response['value'] = 'listener_list';
                break;
            case 'signals':
                switch ($submode) {
                    case 'map':
                        $response['value'] = 'signal_list?show=map';
                        break;
                    case 'seeklist':
                        $response['value'] = 'signal_seeklist';
                        break;
                    default:
                        $response['value'] = 'signal_list';
                        break;
                }
                break;
        }
        return $response;
    }

    public function get($code)
    {
        return self::SYSTEMS[$code];
    }

    public function getAll()
    {
        return self::SYSTEMS;
    }

    public function getMySQLVersion()
    {
        $stmt = $this->connection->prepare('SELECT VERSION()');
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
