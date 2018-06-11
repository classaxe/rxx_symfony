<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-10
 * Time: 16:03
 */

namespace App\Service;

use App\Service\Visitor;
use Doctrine\ORM\EntityManagerInterface;
use MaxMind\Db\Reader\InvalidDatabaseException;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Database\Reader;

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

    /**
     * GeoService constructor.
     * @param \App\Service\Visitor $visitor
     */
    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @return string - reu|rna|rww - depending on where visitor's IP address originates
     */
    public function getDefaultSystem()
    {
        $ip = $this->visitor->getIpAddress();
        try {
            $reader = new Reader('/usr/local/share/GeoIP/GeoIP2-City.mmdb');
        } catch (InvalidDatabaseException $e) {
            return 'rna';
        }
        try {
            $record = $reader->city($ip);
            switch ($record->continent->code) {
                case 'NA':
                    return 'rna';
                case 'EU':
                    return 'reu';
                default:
                    return 'rww';
            }
        } catch (AddressNotFoundException $e) {
            return 'rna';
        }
    }

}