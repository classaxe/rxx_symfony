<?php

namespace App\Repository;

use App\Entity\Icao;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class IcaoRepository extends ServiceEntityRepository
{
    const DATA_ORIGIN = "https://www.aviationweather.gov/docs/metar/stations.txt";

    private $connection;
    private $translator;

    public function __construct(
        ManagerRegistry $registry,
        Connection $connection,
        TranslatorInterface $translator
    ) {
        parent::__construct($registry, Icao::class);
        $this->connection = $connection;
        $this->translator = $translator;
    }

    /**
     * @param $lat
     * @param $lon
     * @param $limit
     * @param bool $icao
     * @return int|mixed|string
     */
    public function getLocalIcaos($lat, $lon, $limit, $icao = false)
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->select(
                "i.id,\n"
                ."i.cnt,\n"
                ."i.elevation,\n"
                ."i.gsq,\n"
                ."i.icao,\n"
                ."i.lat,\n"
                ."i.lon,\n"
                ."i.name,\n"
                ."i.sp,\n"
                ."ROUND(\n"
                ."  DEGREES(\n"
                ."    ACOS(\n"
                ."      (\n"
                ."        SIN(RADIANS(:lat)) *\n"
                ."        SIN(RADIANS(i.lat))\n"
                ."      ) +\n"
                ."      (\n"
                ."        COS(RADIANS(:lat)) *\n"
                ."        COS(RADIANS(i.lat)) *\n"
                ."        COS(RADIANS(:lon - i.lon))\n"
                ."      )\n"
                ."    )\n"
                ."  ) * 111.05,\n"
                ."  2\n"
                .") AS km,\n"
                ."ROUND(\n"
                ."  DEGREES(\n"
                ."    ACOS(\n"
                ."      (\n"
                ."        SIN(RADIANS(:lat)) *\n"
                ."        SIN(RADIANS(i.lat))\n"
                ."      ) +\n"
                ."      (\n"
                ."        COS(RADIANS(:lat)) *\n"
                ."        COS(RADIANS(i.lat)) *\n"
                ."        COS(RADIANS(:lon - i.lon))\n"
                ."      )\n"
                ."    )\n"
                ."  ) * 69,\n"
                ."  2\n"
                .") AS mi\n"
            )
            ->setParameter('lat', $lat)
            ->setParameter('lon', $lon);
        if ($icao) {
            $qb
                ->andWhere('i.icao = :icao')
                ->setParameter('icao', $icao);
        }
        $qb
            ->orderBy('km', 'ASC')
            ->setMaxResults($limit);

        $result = $qb->getQuery()->execute();
        return $result;
    }

    /**
     * @param $lat
     * @param $lon
     * @param $limit
     * @return array
     */
    public function getMatchingOptions($lat, $lon, $limit)
    {
        $icaos = $this->getLocalIcaos($lat, $lon, $limit);
        $out = [];
        foreach ($icaos as $row) {
            $out[$row['icao']." (".$row['mi']." Miles, ".$row['km']." KM)"] = $row['icao'];
        }
        return $out;
    }

    /**
     * @param $ICAO
     * @param $hours
     * @return array
     */
    public static function getMetar($ICAO, $hours)
    {
        $url = "http://www.aviationweather.gov/adds/metars?station_ids=$ICAO&std_trans=standard&chk_metars=on&hoursStr=past+$hours+hours";
        $out = [];
        if ($my_file = implode(' ', file($url))) {
            $lines =   explode("<", $my_file);
            foreach ($lines as $line) {
                preg_match("/FONT FACE=\"Monospace,Courier\">([0-9a-zA-Z \r\n\t\f\/\-]+)/", $line, $result);
                if ($result) {
                    $alt =      "";
                    $slp =      "";
                    $j =        1;
                    $row =      $result[1];
                    $fields =   explode(" ", $row);
                    $date =     substr($fields[$j], 0, 2);
                    $time =     substr($fields[$j], 2, 4);
                    $j++;       // Skip over station ID

                    while ($j<count($fields)) {
                        if (preg_match("/(SLP[0-9]+)/i", $fields[$j], $tmp)) {
                            $slp = substr($tmp[1], 3);
                        }
                        if (preg_match("/(Q[0-9]+)/i", $fields[$j], $tmp)) {
                            $alt = (float) substr($tmp[1], 1);
                        }
                        if (preg_match("/(A[0-9]+)/i", $fields[$j], $tmp)) {
                            $alt = floor(substr($tmp[1], 1) * 3.38674)/10;
                        }
                        $j++;
                    }
                    if ($alt) {
                        $out[] =    $date." ".$time." ".str_pad($alt, 6).($slp ? " ".($alt>=1000 ? substr($alt, 0, 2) : substr($alt, 0, 1)).substr($slp, 0, 2).".".substr($slp, 2, 1) : "");
                        $alt =  0;
                        $slp =  0;
                    }
                }
            }
        }
        return $out;
    }

    public function i18n($id, array $parameters = [], $domain = null, $_locale = null) {
        return $this->translator->trans($id, $parameters, $domain, $_locale);
    }

    /**
     * @return array
     */
    public function updateIcaoList()
    {
        $lines = @file(static::DATA_ORIGIN);
        if (!$lines) {
            return [
                'error' => sprintf(
                    $this->i18n("Unable to download ICAO data from %s"),static::DATA_ORIGIN
                ),
                'affected' => 0
            ];
        }
        $data = [];
        foreach ($lines as $line) {
            if (substr($line, 62, 1)!=="X") {
                continue;
            }
            $lat =  ('S' === substr($line, 45, 1) ? -1 : 1)
                    * ((int) trim(substr($line, 39, 2)) + (int) substr($line, 42, 2) /60);
            $lon =  ('W' === substr($line, 53, 1) ? -1 : 1)
                    * ((int) trim(substr($line, 47, 3)) + (int) substr($line, 51, 2) /60);
            $gsq =  Rxx::convertDegreesToGSQ($lat, $lon);
            $data[] = [
                'name' =>       addslashes(trim(substr($line, 3, 17))),
                'SP' =>         trim(substr($line, 0, 3)),
                'CNT' =>        trim(substr($line, 81, 2)),
                'ICAO' =>       trim(substr($line, 20, 5)),
                'elevation' =>  trim(substr($line, 56, 4)),
                'lat' =>        $lat,
                'lon' =>        $lon,
                'GSQ' =>        $gsq
            ];
        }
        $sql = "DELETE FROM icao";
        /** @var Doctrine\DBAL\Driver\Statement $stmt */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        foreach ($data as $d) {
            $sql = <<< EOD
                INSERT INTO
                    icao
                SET
                    `name` = '{$d['name']}',
                    `CNT` = '{$d['CNT']}',
                    `elevation` = '{$d['elevation']}',
                    `GSQ` = '{$d['GSQ']}',
                    `ICAO` = '{$d['ICAO']}',
                    `lat` = {$d['lat']},
                    `lon` = {$d['lon']},
                    `SP` = '{$d['SP']}';
EOD;
        /** @var Doctrine\DBAL\Driver\Statement $stmt */
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
        }
        return [
            'error' => false,
            'affected' => count($data)
        ];
    }
}
