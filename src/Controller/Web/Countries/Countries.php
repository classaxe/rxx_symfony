<?php
namespace App\Controller\Web\Countries;

use App\Controller\Web\Base;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Countries
 * @package App\Controller\Web
 */
class Countries extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/countries/{filter}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"=""},
     *     name="countries"
     * )
     * @param $_locale
     * @param $system
     * @param $filter
     * @return Response
     */
    public function controller($_locale, $system, $filter)
    {
        $filter = $filter ?? '*';
        $parameters = [
            '_locale' =>    $_locale,
            'filter' =>     $filter,
            'mode' =>       'Country Code Locator',
            'regions' =>    $this->regionRepository->getAllWithCountries($filter),
            'system' =>     $system
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('countries/index.html.twig', $parameters);
    }
}
