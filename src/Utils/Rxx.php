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
    public static function y($var)
    {
        return "<pre>".print_r($var, true)."</pre>";
    }

    public static function debug($var)
    {
        return new Response(static::y($var));
    }

}