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
     *     "/{_locale}/{system}/listeners/{id}/export/signals_csv",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_signals_csv"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository
     * @return RedirectResponse|Response
     */
    public function csv(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        return $this->export($_locale, $system, $id, 'csv', $listenerRepository);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/export/signals_txt",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_signals_txt"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository
     * @return RedirectResponse|Response
     */
    public function txt(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        return $this->export($_locale, $system, $id, 'txt', $listenerRepository);
    }

    /**
     * @param $_locale
     * @param $system
     * @param $id
     * @param $mode
     * @param ListenerRepository $listenerRepository
     * @return RedirectResponse|Response
     */
    private function export(
        $_locale,
        $system,
        $id,
        $mode,
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
            'subtitle' =>           '(' . count($signals) . ' records sorted by Frequency and ID)',
            'system' =>             $system,
            'listener' =>           $listener,
            'signals' =>            $signals
        ];
        $parameters =   array_merge($parameters, $this->parameters);
        switch ($mode) {
            case 'csv':
                $response = $this->render("listener/export/signals.csv.twig", $parameters);
                break;
            case 'txt':
                $response = $this->render("listener/export/signals.txt.twig", $parameters);
                break;
        }
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename=listener_{$id}_signals.{$mode}");
        return $response;
    }
}
