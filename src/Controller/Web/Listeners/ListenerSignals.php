<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerSignals as ListenerSignalsForm;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class ListenerSignals extends Base
{
    const defaultlimit =    20;
    const defaultSorting =  'khz';
    const defaultOrder =    'a';

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/signals/{type}",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"type"=""},
     *     name="listener_signals"
     * )
     */
    public function signalsController(
        $_locale,
        $system,
        $id,
        $type,
        Request $request,
        ListenerSignalsForm $form,
        ListenerRepository $listenerRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $totalSignals = $listener->getCountSignals();
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $totalSignals
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $totalSignals,
            'type' =>           $type
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $args['total'] = $totalSignals;
        }
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $listenerRepository->getSignalsColumns(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            'of '.$options['total'].' signals',
            'mode' =>               'Signals received by '.$listener->getFormattedNameAndLocation(),
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
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
