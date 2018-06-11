<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-08
 * Time: 12:34
 */

namespace App\Utils;


class Rxx
{
    public static function y($var) {
        return "<pre>".print_r($var, true)."</pre>";
    }
}