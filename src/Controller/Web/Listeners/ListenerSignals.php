<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerSignals as Form;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class ListenerSignals extends Base
{
    const defaultlimit =    1000;
    const defaultSorting =  'khz';
    const defaultOrder =    'a';

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/signals",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_signals"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form
    ) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $listener->getCountSignals(),
            'listenerID' =>     $id
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $columns =                  $this->listenerRepository->getColumns('signals');
        $signals =                  $this->signalRepository->getSignals($args, $columns);
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            'of ' . $args['total'] . ' signals',
            'mode' =>               'Signals | ' . $listener->getFormattedNameAndLocation(),
            'results' => [
                'limit' =>              $args['limit'],
                'page' =>               $args['page'],
                'total' =>              $args['total']
            ],
            'signals' =>            $signals,
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin),
            'typeRepository' =>     $this->typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/signals.html.twig', $parameters);
    }
}
