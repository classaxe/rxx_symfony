<?php
namespace App\Controller\Web\Listener;

use App\Controller\Web\Base;
use App\Form\ListenerSignals as ListenerSignalsForm;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class Signals extends Base
{

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
        $defaultlimit =     100;
        $maxNoPaging =      100;

        if ((int) $id) {
            $listener = $listenerRepository->find((int)$id);
            if (!$listener) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }

        $options = [
            'limit' =>          $defaultlimit,
            'maxNoPaging' =>    $maxNoPaging,
            'page' =>           0,
            'total' =>          $listener->getCountSignals()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          $defaultlimit,
            'page' =>           0,
            'order' =>          'a',
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
