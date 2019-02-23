<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FilterObfuscateEmail extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('obfuscateEmail', [$this, 'obfuscateEmail']),
        ];
    }

    public function obfuscateEmail($string)
    {
        return
            ' '
            . strrev(
                str_replace(
                    '@',
                    '#',
                    $string
                )
            )
            . ' ';
    }
}