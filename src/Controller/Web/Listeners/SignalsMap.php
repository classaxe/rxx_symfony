<?php
namespace App\Controller\Web\Listeners;

use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class SignalsMap extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/signals/map",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_signalsmap"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository
     * @param LogRepository $logRepository
     * @param TypeRepository $typeRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute(
                'listeners',
                ['_locale' => $_locale, 'system' => $system]
            );
        }

        $signals = $listenerRepository->getSignalsForListener($id, [ 'sort' => 'khz', 'latlon' => true ]);

        $types = [];
        foreach ($signals as $s) {
            $types[$s['type']] = $typeRepository->getTypeForCode($s['type']);
        }
        uasort($types, [ $typeRepository, 'sortByOrder' ]);
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'listener' =>           $listener,
            'logs' =>               $logRepository->getLogsForListener($id),
            'mode' =>               strToUpper($system).' Map of Signals received by '.$listener->getName(),
            'signals' =>            $signals,
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener, $this->parameters['isAdmin']),
            'types' =>              $types,
            'typeRepository' =>     $typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/signalmap.html.twig', $parameters);

        return $response;
    }
}
