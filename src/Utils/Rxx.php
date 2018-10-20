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
        return "<pre>".str_replace("Array\n(", "Array (", print_r($var, true))."</pre>";
    }

    public static function getUtcDateTime($yyyymmdd)
    {
        $bits = explode('-', $yyyymmdd);
        $dateTime = new \DateTime();
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        $dateTime->setDate($bits[0], $bits[1], $bits[2]);
        return $dateTime;
    }

    public static function translateChars($string)
    {
        $arr_search = array(  "\n\r",
            "\n",
            "\r\n",
            "\r",
            "\\\"",
            "\\'",
            "&quot;",
            "&deg;",
            "&egrave;",
            "&aelig;",
            "&amp;",
            "&aacute;",
            "&acirc;",
            "&aring;",
            "&atilde;",
            "&auml;",
            "&ccedil;",
            "&eacute;",
            "&ecirc;",
            "&iacute;",
            "&Icirc;",
            "&ocirc;",
            "&Ouml;",
            "&oslash;",
            "&Oslash;",
            "&ouml;",
            "&uuml;",
            "&Uuml;"
        );
        $arr_replace= array(  " ",
            " ",
            " ",
            " ",
            "\"",
            "'",
            "\"",
            "°",
            "è",
            "æ",
            "&",
            "á",
            "â",
            "å",
            "ã",
            "ä",
            "ç",
            "é",
            "ê",
            "í",
            "Î",
            "ô",
            "Ö",
            "ø",
            "Ø",
            "ö",
            "ü",
            "Ü"
        );
        return str_replace($arr_search, $arr_replace, $string);
    }
}
