<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-11
 * Time: 07:49
 */

namespace App\Service;

use App\Entity\Region as RegionEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Region
 * @package App\Service
 */
class Region
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Region constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param null $regions
     * @return array
     */
    public function getRegions($regions = null)
    {
        $filter = ($regions ? ['region' => explode(',', $regions)] : []);

        return
            $this->em
                ->getRepository(RegionEntity::class)
                ->findBy($filter, ['id' => 'ASC']);
    }

    /**
     * @param $baseUrl
     * @param $region
     * @return bool|string
     */
    public function getMapUrlForRegion($baseUrl, $region)
    {
        switch($region) {
            case "af":
                return $baseUrl.'map_af';
            case "as":
                return $baseUrl.'map_as';
            case "ca":
                return $baseUrl.'map_na';
            case "eu":
                return $baseUrl.'map_eu';
            case "na":
                return $baseUrl.'map_na';
            case "sa":
                return $baseUrl.'map_sa';
            default:
                return false;
        }
    }
}