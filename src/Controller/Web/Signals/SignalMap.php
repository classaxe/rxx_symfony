<?php
namespace App\Controller\Web\Signals;

use App\Repository\ListenerRepository;
use App\Repository\MapRepository;
use App\Repository\SignalRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Signals
 */
class SignalMap extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_map"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param SignalRepository $signalRepository,
     * @return RedirectResponse|Response
     */
    public function controllerMap(
        $_locale,
        $system,
        $id,
        SignalRepository $signalRepository
    ) {
        $i18n =     $this->translator;
        $title =    $i18n->trans('Map for %s');

        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'lat' =>                $signal->getLat(),
            'lon' =>                $signal->getLon(),
            'mode' =>               sprintf($title, $signal->getFormattedIdent()),
            'system' =>             $system,
            'tabs' =>               $signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map/eu",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_rx_map_eu"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository,
     * @param SignalRepository $signalRepository,
     * @return RedirectResponse|Response
     */
    public function controllerRxMapEu(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        SignalRepository $signalRepository
    ) {
        $map =      'eu';
        $i18n =     $this->translator;
        $title =    $i18n->trans('European Reception Map for %s');

        return $this->controllerRxMapHandler($_locale, $system, $id, $map, $title, $listenerRepository, $signalRepository);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map/na",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_rx_map_na"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository,
     * @param SignalRepository $signalRepository,
     * @return RedirectResponse|Response
     */
    public function controllerRxMapNa(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        SignalRepository $signalRepository
    ) {
        $map =      'na';
        $i18n =     $this->translator;
        $title =    $i18n->trans('North American Reception Map for %s');

        return $this->controllerRxMapHandler($_locale, $system, $id, $map, $title, $listenerRepository, $signalRepository);
    }

    private function controllerRxMapHandler(
        $_locale,
        $system,
        $id,
        $map,
        $title,
        ListenerRepository $listenerRepository,
        SignalRepository $signalRepository
    ) {
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $listeners =    $listenerRepository->getSignalListenersMapDetails($map, $id);

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'listeners' =>          $listeners,
            'map' =>                $map,
            'mode' =>               sprintf($title, $signal->getFormattedIdent()),
            'system' =>             $system,
            'tabs' =>               $signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/rx_map.html.twig', $parameters);
    }


    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map/{map}/image",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "map": "eu|na"
     *     },
     *     name="signal_rx_map_image"
     * )
     * @param $id
     * @param $map
     * @param ListenerRepository $listenerRepository,
     * @param MapRepository $mapRepository,
     * @param SignalRepository $signalRepository,
     * @return RedirectResponse|Response
     */
    public function controllerRxMapImage(
        $id,
        $map,
        ListenerRepository $listenerRepository,
        MapRepository $mapRepository,
        SignalRepository $signalRepository
    ) {
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
           throw $this->createNotFoundException('The signal does not exist');
        }
        $listenerSpItus =     $listenerRepository->getSignalListenersSpItus($map);
        $listenerMapCoords =  $listenerRepository->getSignalListenersMapCoords($map, $id);

        $basedIn = $signal->getSp() ?? $signal->getItu();
        $heardIn = $signal->getHeardInArr();

        $i18n = $this->translator;
        $text = [
            'title' =>  $signal->getFormattedIdent(),
            'tx' =>     $i18n->trans('Transmitter'),
            'yes' =>    $i18n->trans('Reported'),
            'no' =>     $i18n->trans('Not Reported'),
            'pri' =>    $i18n->trans('Listener Primary QTH'),
            'sec' =>    $i18n->trans('Listener Other QTH'),
        ];

        $mapRepository->drawMapImage($map, 'station', $basedIn, $listenerSpItus, $listenerMapCoords, $heardIn, $text);
        die;
    }
}
