<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CustomExtensions extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('obfuscateEmail', [$this, '_filterObfuscateEmail']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getEnv', [$this, '_functionGetenv']),
        ];
    }

    public function _filterObfuscateEmail($string)
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

    public function _functionGetenv($varname)
    {
        return getenv($varname);
    }
}
