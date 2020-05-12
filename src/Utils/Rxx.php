<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-08
 * Time: 12:34
 */

namespace App\Utils;

use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Response;

class Rxx
{
    const DEG_MI_MULTIPLIER = 69;
    const DEG_KM_MULTIPLIER = 111.05;

    public static function convertDegreesToGSQ($lat, $lon)
    {
        $letters = "abcdefghijklmnopqrstuvwxyz";
        if ($lat==""||$lon=="") {
            return false;
        }

        $lat =      (float) $lat + 90;
        $lat_a =    strtoUpper(substr($letters, floor($lat/10), 1));
        $lat_b =    floor($lat%10);
        $lat_c =    substr($letters, 24*($lat-(int)$lat), 1);

        $lon =      ((float) $lon + 180)/2;
        $lon_a =    strtoUpper(substr($letters, floor($lon/10), 1));
        $lon_b =    floor($lon%10);
        $lon_c =    substr($letters, 24*($lon-(int)$lon), 1);
        return      $lon_a . $lat_a . $lon_b . $lat_b . $lon_c . $lat_c;
    }

    public static function convertGsqToDegrees($GSQ)
    {
        $GSQ =      strToUpper($GSQ);
        $offset =   (strlen($GSQ)==6 ? 1/48 : 0);

        if (strlen($GSQ) == 4) {
            $GSQ = $GSQ."MM";
        }
        if (!preg_match('/[a-rA-R][a-rA-R][0-9][0-9][a-xA-X][a-xA-X]/i', $GSQ)) {
            return false;
        }
        $lon_d = ord(substr($GSQ, 0, 1))-65;
        $lon_m = substr($GSQ, 2, 1);
        $lon_s = ord(substr($GSQ, 4, 1))-65;

        $lat_d = ord(substr($GSQ, 1, 1))-65;
        $lat_m = substr($GSQ, 3, 1);
        $lat_s = ord(substr($GSQ, 5, 1))-65;

        $lon = (int)round((2 * ($lon_d*10 + $lon_m + $lon_s/24 + $offset) - 180)*10000)/10000;
        $lat = (int)round(($lat_d*10 + $lat_m + $lat_s/24 + $offset - 90)*10000)/10000;

        return [
            "lat" => $lat,
            "lon" => $lon,
            "GSQ" => strtoUpper(substr($GSQ, 0, 4)) . strtoLower(substr($GSQ, 4, 2))
        ];
    }

    public static function convertMMMtoMM($value) {
        $months = explode(',', 'JAN,FEB,MAR,APR,MAY,JUN,JUL,AUG,SEP,OCT,NOV,DEC');
        $idx = array_search(strToUpper($value), $months);
        if (false === $idx) {
            return $idx;
        }
        return str_pad((1 + $idx), 2, '0', STR_PAD_LEFT);
    }


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

    public static function formatBytes($bytes, $precision = 3)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function getDx($qth_lat, $qth_lon, $dx_lat, $dx_lon)
    {
        if (($qth_lat==0 && $qth_lon==0) || ($dx_lat==0 && $dx_lon==0)) {
            return false;
        }
        if ($qth_lat == $dx_lat && $qth_lon==$dx_lon) {
            return ['km' => 0, 'miles' => 0];
        }
        $dlon = ($dx_lon - $qth_lon);
        if (abs($dlon) > 180) {
            $dlon = (360 - abs($dlon))*(0-($dlon/abs($dlon)));
        }
        $rinlat =       $qth_lat * 0.01745;    // convert to radians
        $rfnlat =       $dx_lat * 0.01745;
        $rdlon =        $dlon * 0.01745;
        $rgcdist =      acos(sin($rinlat)*sin($rfnlat)+cos($rinlat)*cos($rfnlat)*cos($rdlon));

        return [
            'km' =>     (int)round(abs($rgcdist) * 6370.614),
            'miles' =>  (int)round(abs($rgcdist) * 3958.284)
        ];
    }

    public static function getUtcDateTime($yyyymmdd)
    {
        $bits = explode('-', $yyyymmdd);
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $dateTime->setDate($bits[0], $bits[1], $bits[2]);
        return $dateTime;
    }

    public static function pad_char($text, $char, $places)
    {
        $text = html_entity_decode($text);

        return
            (mb_strlen($text) > $places ?
                substr($text, 0, $places)
                :
                $text . substr(
                    str_repeat($char, $places),
                    0,
                    $places - mb_strlen($text)
                )
            );
    }

    public static function pad_dot($text, $places)
    {
        return static::pad_char($text, '.', $places);
    }

    /**
     * @param $text
     * @param $places
     * @return mixed
     */
    public static function pad_nbsp($text, $places)
    {
        return static::pad_char($text, ' ', $places);
    }
}
