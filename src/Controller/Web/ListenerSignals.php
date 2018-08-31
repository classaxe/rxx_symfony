<?php
namespace App\Controller\Web;

use App\Repository\ListenerRepository;
use App\Repository\SignalRepository;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerSignals extends Base
{

    /**
     * @Route(
     *     "/{system}/listener/{id}/signals",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_signals"
     * )
     */
    public function listenerSignalsController(
        $system,
        $id,
        ListenerRepository $listenerRepository,
        SignalRepository $signalRepository
    ) {
        if ((int) $id) {
            $listener = $listenerRepository->find((int)$id);
            if (!$listener) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }
        $parameters = [
            'id' =>                 $id,
            'columns' =>            $listenerRepository->getSignalsColumns(),
            'menuOptions' =>        $listenerRepository->getMenuOptions($listener),
            'mode' =>               $listener->getName().' &gt; Signals Received',
            'signals' =>            $signalRepository->getSignalsForListener($id),
            'signalPopup' =>        'width=590,height=640,status=1,scrollbars=1,resizable=1',
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/signals.html.twig', $parameters);
    }
}
