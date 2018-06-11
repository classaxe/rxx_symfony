<?php
namespace App\Controller;

use App\Service\Country as CountryService;
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
     * StateLocator constructor.
     * @param CountryService $countryService
     */
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
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
        $countries = $this->countryService->getCountriesAndStates($filter);

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