<?php
namespace App\Controller\Web\Listener;

use App\Form\ListenerLogs as ListenerLogsForm;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Logs extends Base
{
    const defaultlimit =     20;
    const maxNoPaging =      20;

    /**
     * @Route(
     *     "/{system}/listeners/{id}/logs",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logs"
     * )
     */
    public function logsController(
        $system,
        $id,
        Request $request,
        ListenerLogsForm $form,
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
            'total' =>          $listener->getCountLogs()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          'a',
            'page' =>           0,
            'sort' =>           'logDate',
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $listenerRepository->getLogsColumns(),
            'form' =>               $form->createView(),
            'matched' =>            ($options['total'] > $options['maxNoPaging'] ? 'of '.$options['total']. ' log records.' : ''),
            'mode' =>               'Logs for '.$listener->getFormattedNameAndLocation(),
            'logs' =>               $listenerRepository->getLogsForListener($id, $args),
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'signalPopup' =>        'width=590,height=640,status=1,scrollbars=1,resizable=1',
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
            'typeRepository' =>     $typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/logs.html.twig', $parameters);
    }
}
