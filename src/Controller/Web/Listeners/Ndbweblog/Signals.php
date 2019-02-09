<?php
namespace App\Controller\Web\Listeners\Ndbweblog;

use App\Controller\Web\Listeners\Base;
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
     *     "/{_locale}/{system}/listeners/{id}/ndbweblog/stations.js",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_stations"
     * )
     */
    public function stationsController(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              'NDB Weblog stations for '.$listener->getName(),
            'system' =>             $system,
            'listener' =>           $listener,
            'signals' =>            $listenerRepository->getSignalsForListener($id)
        ];
        $parameters =   array_merge($parameters, $this->parameters);
        $response =     $this->render('listener/ndbweblog/stations.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Content-Disposition','attachment;filename=stations.js');

        return $response;
    }
}
