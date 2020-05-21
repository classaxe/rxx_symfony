<?php

namespace App\Repository;

class LanguageRepository
{
    // TODO: Enable LanguageRepository::ENABLEAUTOSELECT when translations are complete
    const ENABLEAUTOSELECT = false; // If enabled, default language is set according to browser preference

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
