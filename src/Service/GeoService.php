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
use GpsLab\Bundle\GeoIP2Bundle\Reader\ReaderFactory;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Symfony\Component\HttpKernel\KernelInterface;

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
    private $reader;
    private $dbPath;

    /**
     * GeoService constructor.
     * @param \App\Service\Visitor $visitor
     * @param ReaderFactory
     */
    public function __construct(KernelInterface $kernel, Visitor $visitor, ReaderFactory $factory)
    {
        $this->visitor = $visitor;
        $this->dbPath = $kernel->getCacheDir() . '/GeoLite2-City.mmdb';
        try {
            $this->reader = $factory->create('default');
        } catch (Exception $e) {
            $binPath = substr(dirname(__DIR__), 0, -4) . "/bin/";
            die(
                "<h1>GeoIP Error</h1>\n"
                . "Please run this command to download the latest GeoIP Database:</p>"
                . "<pre>{$binPath}console geoip2:update</pre>"
            );
        }
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
            $record = $this->reader->city($ip);
            return $record->continent->code;
        } catch (AddressNotFoundException $e) {
            return 'NA';
        } catch (InvalidDatabaseException $e) {
            return 'NA';
        }
    }

    /**
     * @param $ip
     * @return array
     */
    public function getDetailsForIp($ip)
    {
        if (!$ip) {
            return [ 'Result' => 'No IP address given' ];
        }
        try {
            $record = $this->reader->city($ip);
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
                'GeoIP2Age' =>      date('Y-m-d H:i:s', filemtime($this->dbPath))
            ];
        } catch (AddressNotFoundException $e) {
            return [
                'IP' =>             $ip,
                'Result' =>         'Address not found',
                'GeoIP2DB' =>       $this->dbPath,
                'GeoIP2Age' =>      date('Y-m-d H:i:s', filemtime($this->dbPath))
            ];
        } catch (InvalidDatabaseException $e) {
            return [
                'IP' =>             $ip,
                'Result' =>         'Invalid Database',
                'GeoIP2DB' =>       $this->dbPath,
                'GeoIP2Age' =>      date('Y-m-d H:i:s', filemtime($this->dbPath))
            ];
        } catch (Exception $e) {
            return [
                'IP' =>             $ip,
                'Result' =>         'Invalid Request',
                'GeoIP2DB' =>       $this->dbPath,
                'GeoIP2Age' =>      date('Y-m-d h:i:s', filemtime($this->dbPath))
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
