<?php
namespace App\Controller;

use App\Repository\MapRepository;
use App\Repository\ModeRepository;
use App\Repository\SystemRepository;
use App\Utils\Rxx;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations


/**
 * Class CountryLocator
 * @package App\Controller
 */
class Maps extends Controller {

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
        $parameters = $mapRepository->getMap($area);
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
        MapRepository $mapRepository,
        ModeRepository $modeRepository,
        SystemRepository $systemRepository
    ) {
        $systemMaps =   $mapRepository->getSystemMaps($system);
        $maps =         $mapRepository->getAllMaps();

        $parameters = [
            'mode' =>       'Maps',
            'modes' =>      $modeRepository->getAll(),
            'system' =>     $system,
            'systems' =>    $systemRepository->getAll(),
            'title' =>      $systemMaps['title'],
            'zones' =>      [],
        ];

        foreach($systemMaps['maps'] as $zone) {
            $parameters['zones'][$zone] = $maps[$zone];
        }

//        return Rxx::debug($parameters);
        return $this->render('maps/index.html.twig', $parameters);
    }
}