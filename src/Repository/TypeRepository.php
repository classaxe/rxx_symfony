<?php

namespace App\Repository;

class TypeRepository
{
    const types = [
        1 => [
            'bbggrr' => 'ffd800',
            'class' =>  'DGPS',
            'color' =>  '00d8ff',
            'label' =>  'DGPS',
            'order' =>  0,
            'refs'  =>  [
                'descr' =>  'Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:',
                'links' =>  [
                    [
                        'label' =>  'NDB List PDF (by Frequency)',
                        'url'   =>  'http://www.ndblist.info/dgnavinfo/datamodes/worldDGPSdatabase.pdf'
                    ],
                    [
                        'label' =>  'USCG DGPS Site List',
                        'url'   =>  'http://www.navcen.uscg.gov/?pageName=dgpsSiteInfo&All'
                    ]
                ],
                'title' =>  'Reporting DGPS Stations'
            ],
            'title' =>  'DGPS Station'
        ],
        6 => [
            'bbggrr' => '00b0ff',
            'class' =>  'DSC',
            'color' =>  'ffb000',
            'label' =>  'DSC',
            'order' =>  1,
            'refs'  =>  [],
            'title' =>  'DSC Station'
        ],
        4 => [
            'bbggrr' => 'c0ffb8',
            'class' =>  'HAMBCN',
            'color' =>  'b8ffc0',
            'label' =>  'Ham',
            'order' =>  2,
            'refs'  =>  [
                'descr' =>  'Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:',
                'links' =>  [
                    [
                        'label' =>  'William Hepburn\'s LF List',
                        'url'   =>  'http://www.dxinfocentre.com/ndb.htm'
                    ],
                ],
                'title' =>  'Reporting Ham Beacons'
            ],
            'title' =>  'Amateur Radio Beacon'
        ],
        3 => [
            'bbggrr' => 'd8b8ff',
            'class' =>  'NAVTEX',
            'color' =>  'ffb8d8',
            'label' =>  'Navtex',
            'order' =>  3,
            'refs'  =>  [
                'descr' =>  'Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:',
                'links' =>  [
                    [
                        'label' =>  'LOWFERS',
                        'url'   =>  'http://www.lwca.org/sitepage/part15'
                    ],
                    [
                        'label' =>  'HF',
                        'url'   =>  'http://www.keele.ac.uk/depts/por/28.htm'
                    ],
                    [
                        'label' =>  '50MHz',
                        'url'   =>  'http://www.keele.ac.uk/depts/por/50.htm'
                    ],
                ],
                'title' =>  'Reporting Navtex Stations'
            ],
            'title' =>  'NavTex Station'
        ],
        0 => [
            'bbggrr' => 'ffffff',
            'class' =>  'NDB',
            'color' =>  'ffffff',
            'label' =>  'NDB',
            'order' =>  4,
            'refs'  =>  [
                'descr' =>  'Please use the following list as an additional data source - the ship listings from around 404KHz may prove particularly useful:',
                'links' =>  [
                    [
                        'label' =>  'William Hepburn\'s LF List',
                        'url'   =>  'http://www.dxinfocentre.com/ndb.htm'
                    ]
                ],
                'title' =>  'Reporting NDBs'
            ],
            'title' =>  'NDB Beacon'
        ],
        2 => [
            'bbggrr' => 'b0e0ff',
            'class' =>  'TIME',
            'color' =>  'ffe0b0',
            'label' =>  'Time',
            'order' =>  5,
            'refs'  =>  [],
            'title' =>  'Time Signal Station'
        ],
        5 => [
            'bbggrr' => 'fff8b8',
            'class' =>  'OTHER',
            'color' =>  'b8f8ff',
            'label' =>  'Other',
            'order' =>  6,
            'refs'  =>  [],
            'title' =>  'Other form of transmission'
        ],
    ];

    public function getAll()
    {
        return static::types;
    }

    public function getAllChoices()
    {
        $out = [];
        foreach (static::types as $key => $type) {
            $out[$type['label']] = 'type_'.$type['class'];
        }
        $out['(All)'] = 'type_ALL';
        return $out;
    }

    public function getColorsForCodes()
    {
        $out = [];
        foreach (static::types as $key=>$type) {
            $out[$key] = $type['color'];
        }
        return $out;
    }

    public function getSignalTypesSearched(array $types)
    {
        $out = [];
        foreach (static::types as $key=>$type) {
            if (in_array('type_'.$type['class'], $types)){
                $out[] = $key;
            }
        }
        return $out;
    }

    public function getMapIconColorForCodes()
    {
        $out = [];
        foreach (static::types as $key => $type) {
            $out[$key] = $type['bbggrr'];
        }
        return $out;
    }

    public function getTypeForCode($code)
    {
        return static::types[$code];
    }

    public function sortByOrder($a, $b) {
        if ($a['order'] == $b['order']) {
            return 0;
        }
        return ($a['order'] < $b['order']) ? -1 : 1;
    }
}
