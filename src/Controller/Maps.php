<?php
namespace App\Controller;

use App\Repository\MapRepository;
use App\Service\Region as RegionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use App\Utils\Rxx;

/**
 * Class CountryLocator
 * @package App\Controller
 */
class Maps extends Controller {

    private $mapRepository;

    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

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
    public function map($system, $area)
    {
        $parameters = $this->mapRepository->getMap($area);
        $parameters['system'] = $system;

        return $this->render('maps/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/maps",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="show_maps"
     * )
     */
    public function maps($system)
    {
        $systemMaps =   $this->mapRepository->getSystemMaps($system);
        $maps =         $this->mapRepository->getAllMaps();
        $parameters = [
            'zones' =>   [],
            'mode' =>   'Maps',
            'system' => $system,
            'title' =>  $systemMaps['title']
        ];

        foreach($systemMaps['maps'] as $zone) {
            $parameters['zones'][$zone] = $maps[$zone];
        }

//        return Rxx::debug($parameters);
        return $this->render('maps/index.html.twig', $parameters);
    }
}