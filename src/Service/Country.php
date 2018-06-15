<?php
namespace App\Service;

use App\Entity\Itu as ItuEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\StateProvince as StateProvinceService;

/**
 * Class Country
 * @package App\Service
 */
class Country
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var StateProvince
     */
    private $stateProvinceService;

    /**
     * Country constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em,
        StateProvinceService $stateProvinceService
    ) {
        $this->em = $em;
        $this->stateProvinceService = $stateProvinceService;

    }

    /**
     * @param null $regions
     * @return array
     */
    public function getCountriesForRegion($regions = null)
    {
        $filter = ($regions ? ['region' => explode(',', $regions)] : []);

        return
            $this->em
                ->getRepository(ItuEntity::class)
                ->findBy($filter, ['name' => 'ASC']);
    }

    /**
     * @param null $filter
     * @return mixed
     */
    public function getCountriesAndStates($filter = null) {
        $countries = $this->getCountriesHavingStates($filter);

        foreach($countries as &$country) {
            $code =             $country->getItu();
            $country->states =  $this->stateProvinceService->getStates($code);
            $country->map =     $this->getMapUrlForCountry($code);
        }
        return $countries;
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
     * @param $baseUrl
     * @param $code
     * @return bool|string
     */
    public function getMapUrlForCountry($code)
    {
        switch($code) {
            case "AUS":
                return 'map_au';
            case "CAN":
                return 'map_na';
            case "USA":
                return 'map_na';
            default:
                return false;
        }
    }


}