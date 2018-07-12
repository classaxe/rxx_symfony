<?php
namespace App\Controller;

use App\Repository\MapRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class CountryLocator
 * @package App\Controller
 */
class Maps extends BaseController
{

    /**
     * @Route(
     *     "/{system}/map_{area}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "area": "af|alaska|as|au|eu|japan|na|pacific|polynesia|sa"
     *     },
     *     name="show_map"
     * )
     */
    public function map($system, $area, MapRepository $mapRepository)
    {
        $parameters = $mapRepository->get($area);
        $parameters['system'] = $system;

        return $this->render('maps/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/maps",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="maps"
     * )
     */
    public function mapsController(
        $system,
        MapRepository $mapRepository
    ) {
        $systemMaps =   $mapRepository->getAllForSystem($system);

        $parameters = [
            'mode' =>       'Maps',
            'system' =>     $system,
            'title' =>      $systemMaps['title'],
            'zones' =>      $systemMaps['maps'],
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('maps/index.html.twig', $parameters);
    }
}
