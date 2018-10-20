<?php
namespace App\Controller\Web\Listener\Ndbweblog;

use App\Controller\Web\Listener\Base;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Ndbweblog
 */
class Signals extends Base
{
    /**
     * @Route(
     *     "/{system}/listener/{id}/ndbweblog/stations.js",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_stations"
     * )
     */
    public function stationsController(
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            'title' =>              'NDB Weblog stations for '.$listener->getName(),
            'system' =>             $system,
            'listener' =>           $listener,
            'signals' =>            $listenerRepository->getSignalsForListener($id)
        ];
        $parameters =   array_merge($parameters, $this->parameters);
        $response =     $this->render('listener/ndbweblog/stations.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/vnd.google-earth.kml+xml');
        $response->headers->set('Content-Disposition','attachment;filename=stations.js');
        return $response;
    }
}
