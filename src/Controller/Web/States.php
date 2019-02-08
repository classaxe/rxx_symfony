<?php
namespace App\Controller\Web;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class States
 * @package App\Controller\Web
 */
class States extends AbstractController
{
    /**
     * @var CountryRepository
     */
    private $country;

    /**
     * States constructor.
     * @param CountryRepository $countryService
     */
    public function __construct(CountryRepository $country)
    {
        $this->country = $country;
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
            'countries' => $this->country->getCountriesAndStates($filter)
        ];

        return $this->render('states/index.html.twig', $parameters);
    }
}
