<?php
namespace App\Controller\Web\Signals;

use App\Form\Signals\SignalLogs as Form;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class SignalLogs extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'date';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/logs",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_logs"
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
            'total' =>          $signal->getLogs()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $this->signalRepository->getColumns('logs'),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->i18n('of %s Log Records'), $args['total']),
            'mode' =>               sprintf($this->i18n('Logs for %s'), $signal->getFormattedIdent()),
            'records' =>            $this->signalRepository->getLogsForSignal($id, $args),
            'results' => [
                'limit' =>          $args['limit'],
                'page' =>           $args['page'],
                'total' =>          $args['total']
            ],
            'system' =>             $system,
            'tabs' =>               $this->signalRepository->getTabs($signal)
        ];
        return $this->render('signal/logs.html.twig', $this->getMergedParameters($parameters));
    }
}
