<?php
namespace App\Controller\Web\Logsessions;

use App\Controller\Web\Base;
use App\Entity\User as UserEntity;
use App\Form\LogSessions\LogSessions as Form;
use App\Form\LogSessions\LogSession as LogSessionViewForm;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class LogsessionDelete
 * @package App\Controller\Web
 */
class LogsessionDelete extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{logSessionId}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logsession/delete"
     * )
     * @param $_locale
     * @param $system
     * @param $logSessionId
     * @return RedirectResponse
     */
    public function logSessionDelete(
        $_locale,
        $system,
        $logSessionId
    ) {
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }

        $logSession = $this->logsessionRepository->find((int) $logSessionId);
        if (!$logSession) {
            return $this->redirectToRoute('logsessions', ['_locale' => $_locale, 'system' => $system]);
        }

        $listenerId = $logSession->getListenerId();
        $listener = $this->listenerRepository->find($listenerId);
        $operatorId = $logSession->getOperatorId();

        $args = [
            'order' =>          'd',
            'sort' =>           'logDate',
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

        $this->listenerRepository->updateListenerStats($listenerId);
        if ($operatorId) {
            $this->listenerRepository->updateListenerStats($operatorId);
        }

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->i18n("Log Session has been deleted. All affected signals and stats for %s have been updated."),
                $listener->getName()
            )
        );
        return $this->redirectToRoute('logsessions', ['_locale' => $_locale, 'system' => $system]);
    }
}