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
    public static function debug($var)
    {
        return new Response(static::y($var));
    }

    public static function error($message)
    {
        return new Response("<p><strong>Error:</strong><br />$message</p>");
    }

    public static function y($var)
    {
        return "<pre>".print_r($var, true)."</pre>";
    }

}