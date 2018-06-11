<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-10
 * Time: 16:13
 */

namespace App\Service;


/**
 * Class Visitor
 * @package App\Service
 */
class Visitor
{
    /**
     * @return false|string
     */
    public function getIpAddress()
    {
        /* From Laravel
                return
                    Collection::make([
                    'x',
                    'y',
                    'z'])
                    -first(function ($header) {
                        return getenv($header);
                });
        */
        $ip = getenv('HTTP_CLIENT_IP')?:
            getenv('HTTP_X_FORWARDED_FOR')?:
                getenv('HTTP_X_FORWARDED')?:
                    getenv('HTTP_FORWARDED_FOR')?:
                        getenv('HTTP_FORWARDED')?:
                            getenv('REMOTE_ADDR');

//        $ip = '206.248.171.206'; // Canada, Ontario
//        $ip = '72.130.194.78';   // USA, Minnesota
//        $ip = '213.219.36.56';   // UK, London
//        $ip = '95.31.18.119';    // Russia, Moscow
//        $ip = '202.86.32.122';   // Brisbane, Australia

        return $ip;
    }

}