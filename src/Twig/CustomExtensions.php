<?php

namespace App\Twig;

use Symfony\Component\HttpKernel\Kernel;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class CustomExtensions extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('float', function ($val) { return (float)$val; }),
            new TwigFilter('formatTimeZone', [$this, '_formatTimezone']),
            new TwigFilter('formatNl2br', [$this, '_formatNl2br']),
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

    public function getTests()
    {
        return [
            new TwigTest('numeric', function ($value) { return is_numeric($value); }),
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

    public static function _formatNl2br($text)
    {
        return nl2br($text);
    }

    public static function _formatTimezone($num)
    {
        $sign = ($num < 0 ? '-' : '');
        $whole = (int) $num;  // 5
        $frac  = $num - $whole;

        $hh =   self::lead_zero(abs(floor($whole)),2);
        $mm =   abs($frac * 60);
        return  "$sign$hh:".($mm ? $mm : '00');
    }

    public static function lead_zero($text, $places) {
        return substr("0000", 0, $places - strlen($text)) . $text;
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
        return Kernel::VERSION;
    }

    public function _unescape($value)
    {
        return html_entity_decode($value);
    }
}
