<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-08
 * Time: 12:34
 */

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

class Rxx
{
    const types = array(
        'DGPS' =>     [
            'type' =>   1,
            'color' =>  '#00d8ff',
            'label' =>  'DGPS',
            'width' =>  13
        ],
        'DSC' =>      [
            'type' =>   0,
            'color' => '#ffb000',
            'label' =>  'DSC',
            'width' =>  11
        ],
        'HAMBCN' =>   [
            'type' =>   0,
            'color' => '#b8ffc0',
            'label' =>  'Ham',
            'width' =>  12
        ],
        'NAVTEX' =>   [
            'type' =>   0,
            'color' => '#ffb8d8',
            'label' =>  'Navtex',
            'width' =>  15
        ],
        'NDB' =>      [
            'type' =>   0,
            'color' =>  '#ffffff',
            'label' =>  'NDB',
            'width' =>  11
        ],
        'TIME' =>     [
            'type' =>   0,
            'color' => '#ffe0b0',
            'label' =>  'Time',
            'width' =>  13
        ],
        'OTHER' =>    [
            'type' =>   0,
            'color' => '#b8f8ff',
            'label' =>  'Other',
            'width' =>  13
        ],
        'ALL' =>      [
            'type' =>   99,
            'color' => '#ffb000',
            'label' =>  '(All)',
            'width' =>  12
        ],
    );

    public static function y($var)
    {
        return "<pre>".print_r($var, true)."</pre>";
    }

    public static function debug($var)
    {
        return new Response(static::y($var));
    }

    private function drawControlType()
    {
        $types = array(
            array(DGPS,     'type_DGPS',    'DGPS',     13),
            array(DSC,      'type_DSC',     'DSC',      11),
            array(HAMBCN,   'type_HAMBCN',  'Ham',      12),
            array(NAVTEX,   'type_NAVTEX',  'Navtex',   15),
            array(NDB,      'type_NDB',     'NDB',      11),
            array(TIME,     'type_TIME',    'Time',     13),
            array(OTHER,    'type_OTHER',   'Other',    13),
            array(ALL,      'type_ALL',     '(All)',    12)
        );
        $html = '';
        foreach ($types as $type) {
            $html.=
                "<label style='width:".$type[3]."%;' class='".strToLower($type[1])."'>"
                ."<input type='checkbox' style='vertical-align: middle' name='".$type[1]."' value='1'"
                .($this->{$type[1]} ? " checked='checked'" : "")
                .('type_ALL' == $type[1] ? " onchange=\"set_signal_list_types(document.form, this.checked)\"" : "")
                .">"
                .$type[2]
                ."</label>";
        }
        return $html;
    }

}