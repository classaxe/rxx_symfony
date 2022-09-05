<?php
namespace App\Controller\Web\Users;

use App\Form\Listeners\ListenerLogSessions as Form;
use App\Utils\Rxx;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class UserLogsessionDelete extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/users/{id}/logssession/{logSessionId}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="user_logsession_delete"
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
            return $this->redirectToRoute('users', ['_locale' => $_locale, 'system' => $system]);
        }
        $user = $this->userRepository->find((int) $id);
        if (!$user) {
            return $this->redirectToRoute('users', ['_locale' => $_locale, 'system' => $system]);
        }

        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('user_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        $logSession = $this->logsessionRepository->find((int) $logSessionId);
        if (!$logSession) {
            return $this->redirectToRoute('user_logsessions', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }
        $administratorId =  $logSession->getAdministratorId();
        $listenerId =       $logSession->getListenerId();
        $listener =         $this->listenerRepository->find($listenerId);
        $args = [
            'order' =>          'd',
            'sort' =>           'logDate',
            'listenerId' =>     $listenerId,
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
                if ($logRecord['operatorId']) {
                    $this->listenerRepository->updateListenerStats($logRecord['operatorId']);
                }
            }
        }
        $em->remove($logSession);
        $em->flush();

        $this->listenerRepository->updateListenerStats($listenerId);
        $this->userRepository->updateUserStats($administratorId);

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->i18n("Log Session %s has been deleted. All affected signals and stats for %s have been updated."),
                $logSessionId,
                $listener->getName()
            )
        );
        return $this->redirectToRoute('user_logsessions', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
    }
}
