<?php
namespace App\Controller\Web;

use App\Repository\RegionRepository as Region;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Countries
 * @package App\Controller\Web
 */
class Countries extends AbstractController
{

    /**
     * @var Region
     */
    private $region;

    /**
     * Countries constructor.
     * @param Region $region
     */
    public function __construct(Region $region)
    {
        $this->region = $region;
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
            'regions' => $this->region->getAllWithCountries($filter)
        ];

        return $this->render('countries/index.html.twig', $parameters);
    }
}
