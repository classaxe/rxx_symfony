<?php
namespace App\Controller\Web\States;

use App\Controller\Web\Base;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class States
 * @package App\Controller\Web
 */
class States extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/states/{filter}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"="*"},
     *     name="states"
     * )
     */
    public function stateLocatorController($_locale, $system, $filter)
    {
        $parameters = [
            '_locale' =>    $_locale,
            'countries' =>  $this->countryRepository->getCountriesAndStates($filter),
            'filter' =>     $filter,
            'mode' =>       'State and Province Locator',
            'system' =>     $system,
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('states/index.html.twig', $parameters);
    }
}
