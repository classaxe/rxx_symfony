<?php

namespace App\Repository;

use Symfony\Contracts\Translation\TranslatorInterface;

class TimeRepository
{
    private $translator;

    const TIMEZONES = [
        [  -12, '-12:00', 'Dateline ST',        '' ],
        [  -11, '-11:00', 'Samoa ST',           'Midway Island, Samoa' ],
        [  -10, '-10:00', 'Hawaiian ST',        'Hawaii' ],
        [   -9, '-09:00', 'Alaskan ST',         'Alaska' ],
        [   -8, '-08:00', 'Pacific ST',         'Vancouver, Los Angeles, Tijuana' ],
        [   -7, '-07:00', 'Mountain ST',        'Calgary, Colorado, La Paz' ],
        [   -6, '-06:00', 'Central ST',         'Winnipeg, Dallas, Mexico City' ],
        [   -5, '-05:00', 'Eastern ST',         'Toronto, New York, Lima' ],
        [   -4, '-04:00', 'Atlantic ST',        'Halifax' ],
        [ -3.5, '-03:30', 'Nfld / Labrador ST', 'St Johns' ],
        [   -3, '-03:00', 'S America ST',       'Brasilia, Greenland' ],
        [   -2, '-02:00', 'Mid-Atlantic ST',    '' ],
        [   -1, '-01:00', 'Azores ST',          'Azores, Cape Verde' ],
        [    0, ' 00:00', 'UTC / GMT',          'London, Dublin, Lisbon' ],
        [    1, '+01:00', 'Central Europe ST',  'Oslo, Paris, Warsaw' ],
        [    2, '+02:00', 'E Europe ST',        'Helsinki, Bucharest, Jerusalem' ],
        [    3, '+03:00', 'Russian ST',         'Moscow, Kuwait, Bagdad' ],
        [  3.5, '+03:30', 'Iran ST',            'Tehran' ],
        [    4, '+04:00', 'Arabian ST',         'Abu Dhabi, Yerevan' ],
        [  4.5, '+04:30', 'Afghanistan ST',     'Kabul' ],
        [    5, '+05:00', 'West Asia ST',       'Islamabad, Tashkent' ],
        [  5.5, '+05:30', 'India ST',           'Mumbai, New Delhi' ],
        [ 5.75, '+05:45', 'Nepal ST',           'Kathmandu' ],
        [    6, '+06:00', 'Central Asia ST',    'Dhaka, Novosibirsk' ],
        [  6.5, '+06:30', 'Myanmar ST',         'Rangoon' ],
        [    7, '+07:00', 'SE Asia ST',         'Bangkok, Hanoi, Jakarta' ],
        [    8, '+08:00', 'W Australia ST',     'Beijing, Perth' ],
        [ 8.75, '+08:45', 'CW Australia ST',    'Cocklebiddy' ],
        [    9, '+09:00', 'Tokyo ST',           'Seoul, Tokyo' ],
        [  9.5, '+09:30', 'C Australian ST',    'Darwin, Adelaide' ],
        [   10, '+10:00', 'E Australia ST',     'Canberra, Sydney, Brisbane' ],
        [   11, '+11:00', 'C Pacific ST',       'Solomon Islands, New Caledonia' ],
        [   12, '+12:00', 'New Zealand ST',     'Auckland, Wellington' ],
        [   13, '+13:00', 'Tonga ST',           'Tonga' ]
    ];

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getAllOptions($withAllOption = false)
    {
        $out = [];
        if ($withAllOption) {
            $out = [ $this->translator->trans('(All Timezones)') => 'ALL' ];
        }
        foreach (static::TIMEZONES as $tz) {
            $out[$tz[1] . ' ' . str_pad($tz[2], 20, ' ') . ' | ' . $tz[3]] = $tz[0] ;
        }
        return $out;
    }
}
