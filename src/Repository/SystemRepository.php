<?php

namespace App\Repository;
use Doctrine\DBAL\Driver\Connection;
use DOMDocument;

class SystemRepository
{
    const NEW_VERSION_AGE = 2;
    const REPO_BASE = "https://github.com/classaxe/rxx_symfony";

    const AUTHORS = [
        [
            'callsign' =>   'VA3PHP',
            'email' =>      'martin@classaxe.com',
            'name' =>       'Martin Francis',
            'role' =>       'Software Development',
            'show_email' => true
        ],
        [
            'callsign' =>   'KB8QGF',
            'email' =>      'kb8qgf@gmail.com',
            'name' =>       'Andy Robins',
            'role' =>       'Initial Concept',
            'show_email' => false
        ],
    ];

    const AWARDS = [
        [
            'callsign' =>   'KJ8O',
            'email' =>      'kj8o.ham@gmail.com',
            'name' =>       'Joseph Miller',
            'role' =>       'Awards Coordinator',
            'show_email' => false
        ],
        [
            'callsign' =>   '',
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
                    'callsign' =>   'EI4HQ',
                    'email' =>      'ei4hq.mail@gmail.com',
                    'name' =>       'Cormac',
                    'roles' =>      ['DGPS', 'NAVTEX'],
                    'show_email' => true
                ],
                [
                    'callsign' =>   'IZ8294SWL',
                    'email' =>      'ros.palermo@libero.it',
                    'name' =>       'Rosario Palermo',
                    'roles' =>      ['DSC'],
                    'show_email' => true
                ],
                [
                    'callsign' =>   'VE1VDM',
                    'email' =>      'vmath@eastlink.ca',
                    'name' =>       'Vernon Matheson',
                    'roles' =>      ['NDB'],
                    'show_email' => true
                ],
                [
                    'callsign' =>   'PA0RDT',
                    'email' =>      'roelofndb@delta.nl',
                    'name' =>       'Roelof Bakker',
                    'roles' =>      ['HAMBCN'],
                    'show_email' => true
                ],
            ],
            'menu' =>       'North America (RNA)',
            'menu_s' =>     'RNA',
            'title' =>      'Signals Received in N & C America',
            'title_s' =>    'Received N+C America'
        ],
        'reu' =>    [
            'authors' =>    self::AUTHORS,
            'awards' =>     self::AWARDS,
            'editors' =>    [
                [
                    'callsign' =>   'EI4HQ',
                    'email' =>      'ei4hq.mail@gmail.com',
                    'name' =>       'Cormac',
                    'roles' =>      ['DGPS', 'NAVTEX'],
                    'show_email' => true
                ],
                [
                    'callsign' =>   'IZ8294SWL',
                    'email' =>      'ros.palermo@libero.it',
                    'name' =>       'Rosario Palermo',
                    'roles' =>      ['DSC'],
                    'show_email' => true
                ],
                [
                    'callsign' =>   'PA0RDT',
                    'email' =>      'roelofndb@delta.nl',
                    'name' =>       'Roelof Bakker',
                    'roles' =>      ['NDB', 'HAMBCN'],
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
                    'callsign' =>   'EI4HQ',
                    'email' =>      'ei4hq.mail@gmail.com',
                    'name' =>       'Cormac',
                    'roles' =>      ['DGPS', 'NAVTEX'],
                    'show_email' => true
                ],
                [
                    'callsign' =>   'IZ8294SWL',
                    'email' =>      'ros.palermo@libero.it',
                    'name' =>       'Rosario Palermo',
                    'roles' =>      ['DSC'],
                    'show_email' => true
                ],
                [
                    'callsign' =>   'PA0RDT',
                    'email' =>      'roelofndb@delta.nl',
                    'name' =>       'Roelof Bakker',
                    'roles' =>      ['NDB', 'HAMBCN'],
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

    public function getAdmins()
    {
        $admins = [];
        foreach (SystemRepository::AWARDS as $a) {
            $names = explode(', ', $a['name']);
            foreach ($names as $n) {
                $admins[$n] = $a['email'];
            }
        }
        foreach (SystemRepository::SYSTEMS as $s) {
            foreach ($s['editors'] as $e) {
                $names = explode(', ', $e['name']);
                foreach ($names as $n) {
                    $admins[$n] = $e['email'];
                }
            }
        }
        ksort($admins);
        return $admins;
    }

    public function get($code)
    {
        return self::SYSTEMS[$code];
    }

    public function getAll()
    {
        return self::SYSTEMS;
    }

    public function getGitInfo(int $new_days = 0)
    {
        $changelog = explode("\n", `git log master --pretty=format:"%ad %H %s" --date=short`);
        $entries = [];
        foreach ($changelog as &$entry) {
            $bits =     explode(' ', $entry);
            $date =     array_shift($bits);
            $new =      round(
                $datediff = (time() - strtotime($date)) / (60 * 60 * 24)
            ) <= $new_days;
            $hash =     trim(array_shift($bits), ':');
            $version =  trim(array_shift($bits), ':');
            $details =  implode(' ', $bits);
            $entries[] =
                '<li id="' . $version .'">'
                . '<a href="' . static::REPO_BASE . '/commit/' . $hash .'" target="_blank"><strong>'.$version.'</strong></a> '
                . ' <em>('.$date.')</em> '
                .($new ? '<span class="new">NEW</span> ' : '')
                . '<br />'
                . $details
                . '</li>';
        }
        return $entries;
    }

    public function getPhpInfo() : string
    {
        $doc = new DOMDocument();
        ob_start();
        phpinfo();
        libxml_use_internal_errors(true);
        $doc->loadHtml(ob_get_contents());
        ob_get_clean();
        return $this->innerHTML(
            $doc->getElementsByTagName('div')->item(0)
        );
    }

    private function innerHTML($element) : string
    {
        $doc = $element->ownerDocument;
        $html = '';
        foreach ($element->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }
        return $html;
    }

    public function getMySQLVersion()
    {
        $stmt = $this->connection->prepare('SELECT VERSION()');
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
