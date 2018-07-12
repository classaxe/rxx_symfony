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
use Symfony\Component\DependencyInjection\Container;

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

    private $dbPath = '/usr/local/share/GeoIP/GeoIP2-City.mmdb';

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
        if (!$ip) {
            return "rna";
        }
        try {
            $reader = new Reader($this->dbPath);
        } catch (\Exception $e) {
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
            return "rna";
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
