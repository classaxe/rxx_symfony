<?php
namespace App\Controller;

use App\Service\Region as RegionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class CountryLocator
 * @package App\Controller
 */
class CountryLocator extends Controller {

    /**
     * @var RegionService
     */
    private $regionService;

    /**
     * CountryLocator constructor.
     * @param RegionService $regionService
     */
    public function __construct(RegionService $regionService)
    {
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
        $parameters = [
            'system' => $system,
            'mode' => 'Country Code Locator',
            'regions' => $this->regionService->getRegionsAndCountries($filter)
        ];

        return $this->render('countries/index.html.twig', $parameters);
    }

}