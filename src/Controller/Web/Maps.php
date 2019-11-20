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
    const popups = [
        'itu' => "itu|width=640,height=500,resizable=1",
        'sp' =>  "sp|width=640,height=500,resizable=1"
    ];
    /**
     * @Route(
     *     "/{_locale}/{system}/map/{area}",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "area": "af|alaska|as|au|eu|japan|na|pacific|polynesia|sa"
     *     },
     *     name="map"
     * )
     * @param $_locale
     * @param $system
     * @param $area
     * @param MapRepository $mapRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function map($_locale, $system, $area, MapRepository $mapRepository)
    {
        $parameters = $mapRepository->get($area);
        $parameters['popups'] = self::popups;
        $parameters['_locale'] = $_locale;
        $parameters['system'] = $system;

        return $this->render('maps/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/maps",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="maps"
     * )
     * @param $_locale
     * @param $system
     * @param MapRepository $mapRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mapsController($_locale, $system, MapRepository $mapRepository)
    {
        $maps =   $mapRepository->getAllForSystem($system);

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Maps',
            'popups' =>     self::popups,
            'system' =>     $system,
            'title' =>      $maps['title'],
            'zones' =>      $maps['maps'],
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('maps/index.html.twig', $parameters);
    }
}
