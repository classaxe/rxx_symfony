<?php
namespace App\Controller\Web\Signals;

use App\Form\Signals\SignalListeners as Form;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class SignalListeners extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'name';
    const defaultOrder =    'a';

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/listeners",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_listeners"
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
        if (!$signal = $this->getValidSignal($id)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }

        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $signal->getListeners()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $this->signalRepository->getColumns('listeners'),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->i18n('of %s Listeners'), $args['total']),
            'mode' =>               sprintf($this->i18n('Listeners for %s'), $signal->getFormattedIdent()),
            'records' =>            $this->signalRepository->getListenersForSignal($id, $args),
            'results' => [
                'limit' =>          $args['limit'],
                'page' =>           $args['page'],
                'total' =>          $args['total']
            ],
            'system' =>             $system,
            'tabs' =>               $this->signalRepository->getTabs($signal)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/listeners.html.twig', $parameters);
    }
}
