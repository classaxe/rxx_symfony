<?php

namespace App\Repository;

class LanguageRepository
{
    const LANGUAGES = [
        'en' =>     'English',
        'fr' =>     'Française',
        'de' =>     'Deutsche',
        'es' =>     'Española'
    ];

    public function get($code)
    {
        return self::LANGUAGES[$code];
    }

    public function getAll()
    {
        return self::LANGUAGES;
    }
}
