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
                'title' =>  'Additional DGPS Station Info',
                'links' =>  [
                    [
                        'label' =>  'USCG DGPS Site List',
                        'url'   =>  'http://www.navcen.uscg.gov/?pageName=dgpsSiteInfo&All'
                    ]
                ]
            ],
            'title' =>  'DGPS Station',
            'type' =>   'dgps'
        ],
        6 => [
            'bbggrr' => '00b0ff',
            'class' =>  'DSC',
            'color' =>  'ffb000',
            'label' =>  'DSC',
            'order' =>  1,
            'refs'  =>  [],
            'title' =>  'DSC Station',
            'type' =>   'dsc'
        ],
        4 => [
            'bbggrr' => 'c0ffb8',
            'class' =>  'HAMBCN',
            'color' =>  'b8ffc0',
            'label' =>  'Ham',
            'order' =>  2,
            'refs'  =>  [
                'title' =>  'Additional Ham Beacon Info:',
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
                ]
            ],
            'title' =>  'Amateur Radio Beacon',
            'type' =>   'hambcn'
        ],
        3 => [
            'bbggrr' => 'd8b8ff',
            'class' =>  'NAVTEX',
            'color' =>  'ffb8d8',
            'label' =>  'Navtex',
            'order' =>  3,
            'refs'  =>  [
                'title' =>  'Additional Navtex Station Info:',
                'links' =>  [
                    [
                        'label' =>  'William Hepburn\'s LF List',
                        'url'   =>  'http://www.dxinfocentre.com/navtex.htm'
                    ],
                    [
                        'label' =>  'William Hepburn\'s HF List',
                        'url'   =>  'http://www.dxinfocentre.com/maritimesafetyinfo.htm'
                    ]
                ],
            ],
            'title' =>  'NavTex Station',
            'type' =>   'navtex'
        ],
        0 => [
            'bbggrr' => 'ffffff',
            'class' =>  'NDB',
            'color' =>  'ffffff',
            'label' =>  'NDB',
            'order' =>  4,
            'refs'  =>  [
                'links' =>  [
                    [
                        'label' =>  'William Hepburn\'s LF List',
                        'url'   =>  'http://www.dxinfocentre.com/ndb.htm'
                    ]
                ],
                'title' =>  'Additional NDB Info:'
            ],
            'title' =>  'NDB Beacon',
            'type' =>   'ndb'
        ],
        2 => [
            'bbggrr' => 'b0e0ff',
            'class' =>  'TIME',
            'color' =>  'ffe0b0',
            'label' =>  'Time',
            'order' =>  5,
            'refs'  =>  [],
            'title' =>  'Time Signal Station',
            'type' =>   'time'
        ],
        5 => [
            'bbggrr' => 'fff8b8',
            'class' =>  'OTHER',
            'color' =>  'b8f8ff',
            'label' =>  'Other',
            'order' =>  6,
            'refs'  =>  [],
            'title' =>  'Other form of transmission',
            'type' =>   'other'
        ],
    ];

    public function getAll()
    {
        return static::types;
    }

    public function getAllChoices($withAllOption = false)
    {
        $out = [];
        foreach (static::types as $key => $type) {
            $out[$type['label']] = $type['class'];
        }
        if ($withAllOption) {
            $out['(All)'] = 'ALL';
        }
        return $out;
    }

    public function getAllChoicesForKey()
    {
        $out = [];
        foreach (static::types as $key => $type) {
            $out[$type['label']] = $key;
        }
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
            if (in_array($type['class'], $types)){
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
