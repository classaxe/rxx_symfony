<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-11
 * Time: 07:49
 */

namespace App\Service;

use App\Entity\Itu as ItuEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Country
 * @package App\Service
 */
class Country
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Country constructor.
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
    public function getCountries($regions = null)
    {
        $filter = ($regions ? ['region' => explode(',', $regions)] : []);

        return
            $this->em
                ->getRepository(ItuEntity::class)
                ->findBy($filter, ['name' => 'ASC']);
    }

    /**
     * @param null $countries
     * @return array
     */
    public function getCountriesHavingStates($countries = null)
    {
        $filter = ($countries ? ['itu' => explode(',', $countries), 'hasSp' => 1] : ['hasSp' => 1]);

        return
            $this->em
                ->getRepository(ItuEntity::class)
                ->findBy($filter, ['name' => 'ASC']);
    }

    /**
     * @param $code
     * @return int
     */
    public function getColumnsForCountryStates($code)
    {
        switch($code)  {
            case "AUS":
                return  2;
            case "CAN":
                return  3;
            case "USA":
                return  3;
            default:
                return  2;
        }
    }

    /**
     * @param $baseUrl
     * @param $code
     * @return bool|string
     */
    public function getMapUrlForCountry($baseUrl, $code)
    {
        switch($code) {
            case "AUS":
                return $baseUrl.'map_au';
            case "CAN":
                return $baseUrl.'map_na';
            case "USA":
                return $baseUrl.'map_na';
            default:
                return false;
        }
    }


}