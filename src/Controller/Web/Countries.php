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
     *     "/{_locale}/{system}/countries/{filter}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"="*"},
     *     name="countries"
     * )
     */
    public function countryLocatorController($_locale, $system, $filter)
    {
        $parameters = [
            '_locale' =>    $_locale,
            'filter' =>     $filter,
            'mode' =>       'Country Code Locator',
            'regions' =>    $this->region->getAllWithCountries($filter),
            'system' =>     $system
        ];

        return $this->render('countries/index.html.twig', $parameters);
    }
}
