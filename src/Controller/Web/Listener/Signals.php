<?php
namespace App\Controller\Web\Listener;

use App\Form\ListenerSignals as ListenerSignalsForm;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class Signals extends Base
{
    const defaultlimit =     20;
    const maxNoPaging =      20;

    /**
     * @Route(
     *     "/{system}/listeners/{id}/signals",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_signals"
     * )
     */
    public function signalsController(
        $system,
        $id,
        Request $request,
        ListenerSignalsForm $form,
        ListenerRepository $listenerRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $options = [
            'limit' =>          static::defaultlimit,
            'maxNoPaging' =>    static::maxNoPaging,
            'page' =>           0,
            'total' =>          $listener->getCountSignals()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          'a',
            'page' =>           0,
            'sort' =>           'khz'
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $listenerRepository->getSignalsColumns(),
            'form' =>               $form->createView(),
            'matched' =>            ($options['total'] > $options['maxNoPaging'] ? 'of '.$options['total'].' signals' : ''),
            'mode' =>               'Signals received by '.$listener->getFormattedNameAndLocation(),
            'signals' =>            $listenerRepository->getSignalsForListener($id, $args),
            'signalPopup' =>        'width=590,height=640,status=1,scrollbars=1,resizable=1',
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
            'typeRepository' =>     $typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/signals.html.twig', $parameters);
    }
}
