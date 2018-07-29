<?php
namespace App\Controller;

use App\Service\Region as RegionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Countries
 * @package App\Controller
 */
class Countries extends Controller
{

    /**
     * @var RegionService
     */
    private $regionService;

    /**
     * Countries constructor.
     * @param RegionService $regionService
     */
    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

    /**
     * @Route(
     *     "/{system}/countries/{filter}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"=""},
     *     name="countries"
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
