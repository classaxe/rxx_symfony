<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MaxMind\Db\Reader\InvalidDatabaseException;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Database\Reader;

class DefaultSystem extends Controller {

    /**
     * @return string
     */
    private static function getIpAddress()
    {
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

    /**
     * @return string - reu|rna|rww - depending on where visitor's IP address originates
     */
    private static function getDefaultSystem()
    {
        $ip = static::getIpAddress();
        try {
            $reader = new Reader('/usr/local/share/GeoIP/GeoIP2-City.mmdb');
        } catch (InvalidDatabaseException $e) {
            return 'rna';
        }
        try {
            $record = $reader->city($ip);
            switch ($record->continent->code) {
                case 'NA':
                    $system = 'rna';
                    break;
                case 'EU':
                    $system = 'reu';
                    break;
                default:
                    $system = 'rww';
                    break;
            }
        } catch (AddressNotFoundException $e) {
            $system = 'rna';
        }

        return $system;
    }

    /**
     * @Route(
     *     "/",
     *     name="home"
     * )
     */
    public function defaultSystemController()
    {
        $system = static::getDefaultSystem();
        return $this->redirectToRoute("system", array('system' => $system));
    }
}