<?php
namespace App\Service;

use App\Entity\Region as RegionEntity;
use App\Service\Country as CountryService;
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
    private $em;

    /**
     * @var Country
     */
    private $countryService;

    /**
     * Region constructor.
     * @param EntityManagerInterface $em
     * @param Country $countryService
     */
    public function __construct(
        EntityManagerInterface $em,
        CountryService $countryService
    ) {
        $this->em = $em;
        $this->countryService = $countryService;
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
     * @param $filter
     * @return array
     */
    public function getRegionsAndCountries($filter)
    {
        $regions = $this->getRegions($filter);
        foreach ($regions as &$region) {
            $code =                 $region->getRegion();
            $region->countries =    $this->countryService->getCountriesForRegion($code);
        }
        return $regions;
    }
}
