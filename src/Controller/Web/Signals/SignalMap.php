<?php
namespace App\Controller\Web\Signals;

use App\Repository\ListenerRepository;
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
     * @param SignalRepository $signalRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $map,
        Request $request,
        ListenerRepository $listenerRepository,
        SignalRepository $signalRepository
    ) {
        if (!$signal = $this->getValidSignal($id, $signalRepository)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $signalListenerSpItus =     $listenerRepository->getSignalListenersSpItus($map);
        $signalListenerMapCoords =  $listenerRepository->getSignalListenersMapCoords($map);
        $signalListenerMapDetails = $listenerRepository->getSignalListenersMapDetails($map, $id);

        return new Response(
            'Coming soon...',
            Response::HTTP_OK
        );

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               sprintf($this->translator->trans('Map for %s'), $signal->getFormattedIdent()),
            'system' =>             $system,
            'tabs' =>               $signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/weather.html.twig', $parameters);
    }
}
