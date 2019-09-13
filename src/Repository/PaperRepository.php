<?php

namespace App\Repository;

class PaperRepository
{
    const types = [
        'a4' => [
            'cols' => 4,
            'lbl' => 'A4 (Portrait) - 21.6cm x 27.9cm',
            'len' => 755
        ],
        'a4_l' => [
            'cols' => 7,
            'lbl' => 'A4 (Landscape) - 27.9cm x 21.6cm',
            'len' => 470
        ],
        'lgl' => [
            'cols' => 5,
            'lbl' => 'Legal (Portrait) - 8.5" x 14"',
            'len' => 906
        ],
        'lgl_l' => [
            'cols' => 9,
            'lbl' => 'Legal (Landscape) - 14" x 8.5"',
            'len' => 490
        ],
        'ltr' => [
            'cols' => 5,
            'lbl' => 'Letter (Portrait) - 8.5" x 11"',
            'len' => 710
        ],
        'ltr_l' => [
            'cols' => 6,
            'lbl' => 'Letter (Landscape) - 11" x 8.5"',
            'len' => 490
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
}
