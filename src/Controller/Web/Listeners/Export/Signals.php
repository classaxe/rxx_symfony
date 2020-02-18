<?php
namespace App\Controller\Web\Listeners\Export;

use App\Controller\Web\Listeners\Base;
use App\Repository\ListenerRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Export
 */
class Signals extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/export/signals",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_signals"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute(
                'listeners',
                [ '_locale' => $_locale, 'system' => $system ]
            );
        }
        $signals = $listenerRepository->getSignalsForListener($id);
        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              strToUpper($system) . ' signals for '.$listener->getName() . " on " . date('Y-m-d'),
            'subtitle' =>           '(' . count($signals) . ' records sorted by Frequency and callsign and Time)',
            'system' =>             $system,
            'listener' =>           $listener,
            'signals' =>            $signals
        ];
        $parameters =   array_merge($parameters, $this->parameters);
        $response = $this->render('listener/export/signals.txt.twig', $parameters);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
}
