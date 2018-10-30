<?php

namespace App\Repository;

use App\Entity\Icao;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class IcaoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Icao::class);
    }


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

    public function getMatchingOptions($lat, $lon, $limit)
    {
        $icaos = $this->getLocalIcaos($lat, $lon, $limit);
        $out = [];
        foreach ($icaos as $row) {
            $out[$row['icao']." (".$row['mi']." Miles, ".$row['km']." KM)"] = $row['icao'];
        }
        return $out;
    }

    public static function getMetar($ICAO, $hours)
    {
        $url = "http://www.aviationweather.gov/adds/metars/index.php?station_ids=$ICAO&std_trans=standard&chk_metars=on&hoursStr=past+$hours+hours";
        $out = [];
        if ($my_file = @implode(file("$url"), " ")) {
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
}
