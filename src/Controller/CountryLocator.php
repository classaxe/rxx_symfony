<?php
namespace App\Controller;

use App\Service\Country as CountryService;
use App\Service\Region as RegionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class CountryLocator
 * @package App\Controller
 */
class CountryLocator extends Controller {
    /**
     * @var CountryService
     */
    private $countryService;

    /**
     * @var RegionService
     */
    private $regionService;

    /**
     * CountryLocator constructor.
     * @param CountryService $countryService
     * @param RegionService $regionService
     */
    public function __construct(
        CountryService $countryService,
        RegionService $regionService
    ) {
        $this->countryService = $countryService;
        $this->regionService = $regionService;
    }

    /**
     * @Route(
     *     "/{system}/show_itu/{filter}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"=""},
     *     name="show_itu"
     * )
     */
    public function countryLocatorController($system, $filter)
    {
        $baseUrl = $this->generateUrl('system', array('system' => $system));
        $regions = $this->regionService->getRegions($filter);
        foreach ($regions as &$region) {
            $code =                 $region->getRegion();
            $region->map =          $this->regionService->getMapUrlForRegion($baseUrl, $code);
            $region->countries =    $this->countryService->getCountries($code);
            $region->columns = 2;
        }

        return
            $this->render(
                'countries/index.html.twig',
                array(
                    'system' => $system,
                    'mode' => 'Country Code Locator',
                    'regions' => $regions
                )
            );
    }

}