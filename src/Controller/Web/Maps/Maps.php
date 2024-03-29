<?php
namespace App\Controller\Web\Maps;

use App\Controller\Web\Base;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Maps
 * @package App\Controller\Web\Maps
 */
class Maps extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/maps",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="maps"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function mapsController($_locale, $system)
    {
        $maps =   $this->mapRepository->getAllForSystem($system);

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Maps',
            'system' =>     $system,
            'title' =>      $maps['title'],
            'zones' =>      $maps['maps'],
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('maps/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/maps/{area}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "area": "af|alaska|as|au|eu|japan|na|pacific|polynesia|sa"
     *     },
     *     name="map"
     * )
     * @param $_locale
     * @param $system
     * @param $area
     * @return Response
     */
    public function map($_locale, $system, $area)
    {
        $parameters = $this->mapRepository->get($area);
        $parameters['_locale'] = $_locale;
        $parameters['system'] = $system;

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('maps/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/maps/coords/{lat}/{lon}/{mode}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "mode": "map|photo"
     *     },
     *     name="map_coords"
     * )
     * @param $_locale
     * @param $system
     * @param $lat
     * @param $lon
     * @param $mode
     * @return Response
     */
    public function coords($_locale, $system, $lat, $lon, $mode)
    {
        $parameters = [
            '_locale' =>    $_locale,
            'system' =>     $system,
            'lat' =>        $lat,
            'lon' =>        $lon,
            'mode' =>       $mode
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('maps/coords.html.twig', $parameters);
    }
}
