<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerLogs as Form;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerLogsessions extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'timestamp';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logsessions",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logsessions"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function logSessions(
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
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $listener->getCountLogsessions()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        $args['listenerId'] = $id;
        $columns = $this->listenerRepository->getColumns('logsessions');
        $logSessions = $this->logsessionRepository->getLogsessions($args, $columns);

        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'matched' =>            'of '.$options['total']. ' log sessions.',
            'mode' =>               'Log Sessions Uploaded for '.$listener->getFormattedNameAndLocation(),
            'logsessions' =>        $logSessions,
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin),
            'typeRepository' =>     $this->typeRepository
        ];
        return $this->render('listener/logsessions.html.twig', $this->getMergedParameters($parameters));
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logsessions/{logSessionId}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logsession"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param $logSessionId
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function logSession(
        $_locale,
        $system,
        $id,
        $logSessionId,
        Request $request,
        Form $form
    ) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $args = [
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'order' =>          'd',
            'sort' =>           'logDate',
            'logSessionId' =>   $logSessionId
        ];
        $logs =           $this->logRepository->getLogs($args, $this->listenerRepository->getColumns('logs'));
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $this->listenerRepository->getColumns('logs'),
            '_locale' =>            $_locale,
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'logs' =>               $logs,
            'system' =>             $system,
            'typeRepository' =>     $this->typeRepository
        ];
        return $this->render('listener/logsessionlogs.html.twig', $this->getMergedParameters($parameters));
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logssession/{logSessionId}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logsession_delete"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param $logSessionId
     * @return RedirectResponse
     */
    public function logSessionDelete(
        $_locale,
        $system,
        $id,
        $logSessionId
    ) {
        if (!(int) $id) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }
        $listener = $this->listenerRepository->find((int) $id);
        if (!$listener) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }

        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        $logSession = $this->logsessionRepository->find((int) $logSessionId);
        if (!$logSession) {
            return $this->redirectToRoute('listener_logsessions', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        $args = [
            'order' =>          'd',
            'sort' =>           'logDate',
            'listenerId' =>     $id,
            'logSessionId' =>   (int) $logSessionId
        ];
        $sortableColumns =  $this->listenerRepository->getColumns('logs');
        $logRecords =       $this->logRepository->getLogs($args, $sortableColumns);

        $em = $this->getDoctrine()->getManager();
        foreach($logRecords as $logRecord) {
            if ($log = $this->logRepository->find($logRecord['log_id'])) {
                $em->remove($log);
                $em->flush();
                $this->signalRepository->updateSignalStats($logRecord['id'], true, true);
            }
        }
        $em->remove($logSession);
        $em->flush();

        $this->listenerRepository->updateListenerStats($id);

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->i18n("Log Session has been deleted. All affected signals and stats for %s have been updated."),
                $listener->getName()
            )
        );
        return $this->redirectToRoute('listener_logsessions', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
    }
}
