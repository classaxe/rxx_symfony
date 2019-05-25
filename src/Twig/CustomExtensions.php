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
            new TwigFilter('float', function ($val) { return (float)$val; })
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getEnv', [$this, '_functionGetenv']),
            new TwigFunction('symfonyVersion', [$this, '_functionGetSymfonyVersion'])
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

    public function _functionGetSymfonyVersion()
    {
        return \Symfony\Component\HttpKernel\Kernel::VERSION;
    }
}
