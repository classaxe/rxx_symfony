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
    const DATA_ORIGIN = "https://aviationweather.gov/data/cache/stations.cache.xml.gz";

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
        $url = "https://aviationweather.gov/cgi-bin/data/metar.php?ids=$ICAO&hours=$hours&order=id%2C-obs&sep=true&format=raw";
        $out = [];
        if ($my_file = implode(' ', file($url))) {
            $lines =   explode("\n", $my_file);
            foreach ($lines as $line) {
                preg_match("/ ([0-9]{6})Z/", $line, $result);
                if (!$result) {
                    continue;
                }
                $day =  substr($result[1], 0, 2);
                $time = substr($result[1], 2, 4);

                preg_match("/ A([0-9]{4})/", $line, $result);
                if (!$result) {
                    continue;
                }
                $alt = number_format($result[1] / 2.953, 1, '.', '');

                preg_match("/ SLP([0-9]{3})/", $line, $result);
                $slp = ($result ?
                    number_format(
                        ((float)$result[1] < 500 ? ($result[1] < 100 ?'100' : '10') : '9')
                        . '' . ($result[1]/10), 1, '.', ''
                        ) : "");

                $out[] = $day . " " . $time
                    . " " . str_pad($alt, 6, " ", STR_PAD_LEFT) . " "
                    . str_pad($slp, 6, " ", STR_PAD_LEFT);
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
        $buffer_size = 4096; // read 4kb at a time
        $file = gzopen(static::DATA_ORIGIN, 'rb');
        $tmp = '/tmp/icao.xml';
        $out_file = fopen($tmp, 'wb');

        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

// Files are done, close files
        fclose($out_file);
        gzclose($file);
        $archive = file_get_contents($tmp);
        if (!$archive) {
            return [
                'error' => sprintf(
                    $this->i18n("Unable to download ICAO data from %s"),static::DATA_ORIGIN
                ),
                'affected' => 0
            ];
        }
        $xml = simplexml_load_string($archive);
        $obj = @json_decode(@json_encode($xml),1);
        $icaos = $obj['data']['Station'];
        //die('okay');
        foreach ($icaos as $icao) {
            $lat =  $icao['latitude'];
            $lon =  $icao['longitude'];
            $gsq =  Rxx::convertDegreesToGSQ($lat, $lon);
            $data[] = [
                'name' =>       addslashes(trim($icao['site'])),
                'SP' =>         $icao['state'],
                'CNT' =>        $icao['country'],
                'ICAO' =>       $icao['station_id'],
                'elevation' =>  $icao['elevation_m'],
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
