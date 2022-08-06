<?php
namespace App\Controller\Web\Logsessions;

use App\Form\Listeners\ListenerSignals as Form;
use App\Controller\Web\Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Logsessions
 */
class LogsessionSignals extends Base
{
    const defaultlimit =    100;
    const defaultSorting =  'khz';
    const defaultOrder =    'a';

    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{id}/signals",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logsession_signals"
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
        if (!$logsession = $this->logsessionRepository->find($id)) {
            return $this->redirectToRoute('logsession', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $total = $logsession->getSignals();
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $total
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'logsessionID' =>   $id,
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $total,
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $args['total'] = $total;
            $args['logsessionID'] = $id;
        }
        $columns = $this->listenerRepository->getColumns('signals');
        $signals = $this->signalRepository->getSignals($args, $columns);
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            'of '.$options['total'].' signals',
            'mode' =>               "Signals | Log Session $id",
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'signals' =>            $signals,
            'system' =>             $system,
            'tabs' =>               $this->logsessionRepository->getTabs($logsession, $isAdmin),
            'typeRepository' =>     $this->typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('logsession/signals.html.twig', $parameters);
    }
}
