<?php
namespace App\Controller;

use App\Service\Country as CountryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class States
 * @package App\Controller
 */
class States extends Controller
{
    /**
     * @var CountryService
     */
    private $countryService;

    /**
     * States constructor.
     * @param CountryService $countryService
     */
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * @Route(
     *     "/{system}/states/{filter}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"=""},
     *     name="states"
     * )
     */
    public function stateLocatorController($system, $filter)
    {
        $parameters = [
            'system' => $system,
            'mode' => 'State and Province Locator',
            'countries' => $this->countryService->getCountriesAndStates($filter)
        ];

        return $this->render('states/index.html.twig', $parameters);
    }
}
