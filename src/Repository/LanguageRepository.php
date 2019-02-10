<?php

namespace App\Repository;

class LanguageRepository
{
    const LANGUAGES = [
        'en' =>     'English',
        'fr' =>     'Français',
        'de' =>     'Deutsche',
        'es' =>     'Español'
    ];

    public function get($code)
    {
        return self::LANGUAGES[$code];
    }

    public function getAll()
    {
        return self::LANGUAGES;
    }

    public function getAllCodes()
    {
        return array_keys(self::LANGUAGES);
    }

}
