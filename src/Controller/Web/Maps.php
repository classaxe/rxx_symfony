<?php
namespace App\Controller\Web;

use App\Repository\MapRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Countries
 * @package App\Controller\Web
 */
class Maps extends Base
{
    /**
     * @Route(
     *     "/{locale}/{system}/map/{area}",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "area": "af|alaska|as|au|eu|japan|na|pacific|polynesia|sa"
     *     },
     *     name="map"
     * )
     */
    public function map($locale, $system, $area, MapRepository $mapRepository)
    {
        $parameters = $mapRepository->get($area);
        $parameters['locale'] = $locale;
        $parameters['system'] = $system;

        return $this->render('maps/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{locale}/{system}/maps",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="maps"
     * )
     */
    public function mapsController(
        $locale,
        $system,
        MapRepository $mapRepository
    ) {
        $systemMaps =   $mapRepository->getAllForSystem($system);

        $parameters = [
            'locale' =>     $locale,
            'mode' =>       'Maps',
            'system' =>     $system,
            'title' =>      $systemMaps['title'],
            'zones' =>      $systemMaps['maps'],
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('maps/index.html.twig', $parameters);
    }
}
