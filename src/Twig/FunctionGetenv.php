<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FunctionGetenv extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'getEnv',
                function($varname)
                {
                    return getenv($varname);
                }
            )
        ];
    }
}