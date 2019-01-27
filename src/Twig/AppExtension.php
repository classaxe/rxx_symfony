<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getEnv', [$this, 'myFunction']),
        ];
    }

    public function myFunction($varname)
    {
        $value = getenv($varname);

        // Do something with $value...

        return $value;
    }
}