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
            new TwigFilter('float', function ($val) { return (float)$val; }),
            new TwigFilter('ireplace', [$this, '_ireplace']),
            new TwigFilter('obfuscateEmail', [$this, '_obfuscateEmail']),
            new TwigFilter('unescape', [$this, '_unescape'])
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getEnv', [$this, '_getenv']),
            new TwigFunction('symfonyVersion', [$this, '_symfonyVersion'])
        ];
    }

    public function _getenv($varname)
    {
        return getenv($varname);
    }

    public function _ireplace($input, array $replace)
    {
        return str_ireplace(array_keys($replace), array_values($replace), $input);
    }

    public function _obfuscateEmail($string)
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

    public function _symfonyVersion()
    {
        return \Symfony\Component\HttpKernel\Kernel::VERSION;
    }

    public function _unescape($value)
    {
        return html_entity_decode($value);
    }
}
