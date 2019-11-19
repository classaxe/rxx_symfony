<?php

namespace App\Repository;

class PaperRepository
{
    const types = [
        'a4' => [
            'cols' => 4,
            'lbl' => 'A4 (Portrait) - 21.6cm x 27.9cm',
            'rows' => 54
        ],
        'a4_l' => [
            'cols' => 6,
            'lbl' => 'A4 (Landscape) - 27.9cm x 21.6cm',
            'rows' => 34
        ],
        'lgl' => [
            'cols' => 4,
            'lbl' => 'Legal (Portrait) - 8.5" x 14"',
            'rows' => 66
        ],
        'lgl_l' => [
            'cols' => 7,
            'lbl' => 'Legal (Landscape) - 14" x 8.5"',
            'rows' => 36
        ],
        'ltr' => [
            'cols' => 4,
            'lbl' => 'Letter (Portrait) - 8.5" x 11"',
            'rows' => 62
        ],
        'ltr_l' => [
            'cols' => 6,
            'lbl' => 'Letter (Landscape) - 11" x 8.5"',
            'rows' => 36
        ]
    ];

    public function getAllChoices()
    {
        $out = [];
        foreach (static::types as $key => $type) {
            $out[$type['lbl']] = $key;
        }
        return $out;
    }

    public static function getDefaultForSystem($system)
    {
        switch ($system) {
            case 'reu':
                return 'a4';
            case 'rna':
            case 'rww':
            default:
                return 'ltr';
        }
    }

    public function getSpecifications($key)
    {
        if (isset(static::types[$key])) {
            return static::types[$key];
        }
        return false;
    }
}
