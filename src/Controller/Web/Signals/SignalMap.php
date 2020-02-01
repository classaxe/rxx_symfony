<?php
namespace App\Controller\Web\Signals;

use App\Repository\ListenerRepository;
use App\Repository\MapRepository;
use App\Repository\SignalRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web\Signals
 */
class SignalMap extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map/{map}",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "map": "eu|na"
     *     },
     *     name="signal_map"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param $map
     * @param Request $request
     * @param ListenerRepository $listenerRepository,
     * @param MapRepository $mapRepository,
     * @param SignalRepository $signalRepository,
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $map,
        Request $request,
        ListenerRepository $listenerRepository,
        MapRepository $mapRepository,
        SignalRepository $signalRepository
    ) {
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $i18n = $this->translator;
        $listenerSpItus =     $listenerRepository->getSignalListenersSpItus($map);
        $listenerMapCoords =  $listenerRepository->getSignalListenersMapCoords($map, $id);
//        $listenerMapDetails = $listenerRepository->getSignalListenersMapDetails($map, $id);

        $basedIn = $signal->getSp() ?? $signal->getItu();
        $heardIn = $signal->getHeardInArr();
        $text = [
            'title' =>  $signal->getFormattedIdent(),
            'tx' =>     $i18n->trans('Transmitter'),
            'yes' =>    $i18n->trans('Reported'),
            'no' =>     $i18n->trans('Not Reported'),
            'pri' =>    $i18n->trans('Listener Primary QTH'),
            'sec' =>    $i18n->trans('Listener Other QTH'),
        ];

        $mapRepository->drawMapImage($map, 'station', $basedIn, $listenerSpItus, $listenerMapCoords, $heardIn, $text);

        return new Response(
            'Coming soon...',
            Response::HTTP_OK
        );

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               sprintf($i18n->trans('Map for %s'), $signal->getFormattedIdent()),
            'system' =>             $system,
            'tabs' =>               $signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/weather.html.twig', $parameters);
    }
}
