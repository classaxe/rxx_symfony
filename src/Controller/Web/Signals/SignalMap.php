<?php
namespace App\Controller\Web\Signals;

use App\Repository\MapRepository;

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
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_map"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function controllerMap(
        $_locale,
        $system,
        $id
    ) {
        $i18n =     $this->translator;
        $title =    $i18n->trans('Map for %s');

        if (!$signal = $this->getValidSignal($id)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'lat' =>                $signal->getLat(),
            'lon' =>                $signal->getLon(),
            'mode' =>               sprintf($title, $signal->getFormattedIdent()),
            'system' =>             $system,
            'tabs' =>               $this->signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/map.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map/eu",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_rx_map_eu"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function controllerRxMapEu(
        $_locale,
        $system,
        $id
    ) {
        $map =      'eu';
        $i18n =     $this->translator;
        $title =    $i18n->trans('European Reception Map for %s');

        return $this->controllerRxMapHandler($_locale, $system, $id, $map, $title);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map/na",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_rx_map_na"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function controllerRxMapNa(
        $_locale,
        $system,
        $id
    ) {
        $map =      'na';
        $i18n =     $this->translator;
        $title =    $i18n->trans('North American Reception Map for %s');

        return $this->controllerRxMapHandler($_locale, $system, $id, $map, $title);
    }

    private function controllerRxMapHandler(
        $_locale,
        $system,
        $id,
        $map,
        $title
    ) {
        if (!$signal = $this->getValidSignal($id)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $listeners =    $this->listenerRepository->getSignalListenersMapDetails($map, $id);

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'listeners' =>          $listeners,
            'map' =>                $map,
            'mode' =>               sprintf($title, $signal->getFormattedIdent()),
            'system' =>             $system,
            'tabs' =>               $this->signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/rx_map.html.twig', $parameters);
    }


    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/map/{map}/image",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "map": "eu|na"
     *     },
     *     name="signal_rx_map_image"
     * )
     * @param $id
     * @param $map
     * @param MapRepository $mapRepository
     * @return void
     */
    public function controllerRxMapImage(
        $id,
        $map,
        MapRepository $mapRepository
    ) {
        if (!$signal = $this->getValidSignal($id)) {
           throw $this->createNotFoundException('The signal does not exist');
        }
        $listenerSpItus =     $this->listenerRepository->getSignalListenersSpItus($map);
        $listenerMapCoords =  $this->listenerRepository->getSignalListenersMapCoords($map, $id);

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

        header('Content-Type: image/gif');

        $mapRepository->drawMapImage($map, 'signal', $basedIn, $listenerSpItus, $listenerMapCoords, $heardIn, $text);
        die;
    }
}
