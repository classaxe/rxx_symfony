<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-10
 * Time: 16:03
 */

namespace App\Service;

use App\Utils\Rxx;
use Exception;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;

/**
 * Class GeoService
 * @package App\Service
 */
class GeoService
{
    /**
     * @var Visitor
     */
    private $visitor;

    private $dbPath = '/usr/share/GeoIP/GeoLite2-City.mmdb';

    /**
     * GeoService constructor.
     * @param \App\Service\Visitor $visitor
     */
    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @return string|void|null
     */
    public function getContinent()
    {
        $ip = $this->visitor->getIpAddress();
        if (!$ip) {
            return 'NA';
        }
        try {
            $reader = new Reader($this->dbPath);
        } catch (Exception $e) {
            $user =     posix_getpwuid(posix_geteuid());
            $uName =    $user['name'];
            $group =    posix_getgrgid($user['gid']);
            $gName =    $group['name'];
            $binPath = substr(dirname(__DIR__), 0, -4)."/bin/";
            die(
                "<h1>GeoIP Error</h1>\n"
                ."<p>{$this->dbPath} is missing.<br />\n"
                ."Please run these commands:</p>"
                ."<pre>sudo mkdir -p ".dirname($this->dbPath).";\n"
                ."sudo chown $uName:$gName ".dirname($this->dbPath).";\n"
                ."{$binPath}console geoip2:update</pre>"
            );
        }
        try {
            $record = $reader->city($ip);
            return $record->continent->code;
        } catch (AddressNotFoundException $e) {
            return 'NA';
        } catch (InvalidDatabaseException $e) {
            return 'NA';
        }
    }

    /**
     * @param $ip
     * @return \GeoIp2\Model\City|string|void
     */
    public function getDetailsForIp($ip)
    {
        if (!$ip) {
            return 'NA';
        }
        try {
            $reader = new Reader($this->dbPath);
        } catch (Exception $e) {
            $user =     posix_getpwuid(posix_geteuid());
            $uName =    $user['name'];
            $group =    posix_getgrgid($user['gid']);
            $gName =    $group['name'];
            $binPath = substr(dirname(__DIR__), 0, -4)."/bin/";
            die(
                "<h1>GeoIP Error</h1>\n"
                ."<p>{$this->dbPath} is missing.<br />\n"
                ."Please run these commands:</p>"
                ."<pre>sudo mkdir -p ".dirname($this->dbPath).";\n"
                ."sudo chown $uName:$gName ".dirname($this->dbPath).";\n"
                ."{$binPath}console geoip2:update</pre>"
            );
        }
        try {
            $record = $reader->city($ip);
            return [
                'IP' =>             $ip,
                'City' =>           $record->city->name,
                'Subdivision' =>    $record->mostSpecificSubdivision->name,
                'ISOCode' =>        $record->mostSpecificSubdivision->isoCode,
                'Country' =>        $record->country->name,
                'Continent' =>      $record->continent->name,
                'Postal' =>         $record->postal->code,
                'Coords' =>         $record->location->latitude . ' / ' . $record->location->longitude,
                'GSQ' =>            Rxx::convertDegreesToGSQ($record->location->latitude, $record->location->longitude),
                'System' =>         strtoupper($this->getDefaultSystem()),
                'GeoIP2DB' =>       $this->dbPath,
                'GeoIP2Age' =>      date('Y-m-d h:i:s', filemtime($this->dbPath))
            ];
        } catch (AddressNotFoundException $e) {
            return [
                'IP' =>             $ip,
                'Result' =>         'Address not found'
            ];
        } catch (InvalidDatabaseException $e) {
            return [
                'IP' =>             $ip,
                'Result' =>         'Invalid Database'
            ];
        } catch (Exception $e) {
            return [
                'IP' =>             $ip,
                'Result' =>         'Invalid Request'
            ];
        }
    }

    /**
     * @return string - reu|rna|rww - depending on where visitor's IP address originates
     */
    public function getDefaultSystem()
    {
        $continent = $this->getContinent();
        switch ($continent) {
            case 'NA':
                return 'rna';
            case 'EU':
                return 'reu';
            default:
                return 'rww';
        }
    }
}
