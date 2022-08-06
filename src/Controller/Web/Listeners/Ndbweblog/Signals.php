<?php
namespace App\Controller\Web\Listeners\Ndbweblog;

use App\Controller\Web\Listeners\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_stations"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id
    ) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $args = ['listenerID' => $id];
        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              'NDB Weblog stations for '.$listener->getName(),
            'system' =>             $system,
            'listener' =>           $listener,
            'signals' =>            $this->signalRepository->getSignals($args)
        ];
        $parameters =   array_merge($parameters, $this->parameters);
        $response =     $this->render('listener/ndbweblog/stations.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Content-Disposition','attachment;filename=stations.js');

        return $response;
    }
}
