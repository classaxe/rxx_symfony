<?php
namespace App\Controller;

use App\Service\Country as CountryService;
use App\Service\StateProvince as StateProvinceService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class StateLocator
 * @package App\Controller
 */
class StateLocator extends Controller {

    /**
     * @var CountryService
     */
    private $countryService;
    /**
     * @var StateProvinceService
     */
    private $stateProvinceService;

    /**
     * CountryLocator constructor.
     * @param CountryService $countryService
     * @param RegionService $regionService
     */
    public function __construct(
        CountryService $countryService,
        StateProvinceService $stateProvinceService
    ) {
        $this->countryService = $countryService;
        $this->stateProvinceService = $stateProvinceService;
    }

    /**
     * @Route(
     *     "/{system}/show_sp/{filter}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"=""},
     *     name="show_sp"
     * )
     */
    public function stateLocatorController($system, $filter)
    {
        $baseUrl = $this->generateUrl('system', array('system' => $system));
        $countries = $this->countryService->getCountriesHavingStates($filter);
        foreach($countries as &$country) {
            $code =             $country->getItu();
            $country->states =  $this->stateProvinceService->getStates($code);
            $country->map =     $this->countryService->getMapUrlForCountry($baseUrl, $code);
            $country->columns = $this->countryService->getColumnsForCountryStates($code);
        }
//        return new Response(Rxx::y($countries));

        return
            $this->render(
                'states/index.html.twig',
                array(
                    'system' => $system,
                    'mode' => 'State and Province Locator',
                    'countries' => $countries
                )
            );
    }

}