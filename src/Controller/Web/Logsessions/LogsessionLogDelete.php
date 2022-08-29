<?php
namespace App\Controller\Web\Logsessions;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class LogsessionLogDelete extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{id}/logs/{log_id}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logsession_log_delete"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param $log_id
     * @return RedirectResponse
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $log_id
    ) {
        if (!(int) $id) {
            return $this->redirectToRoute('logsession_logs', ['_locale' => $_locale, 'system' => $system]);
        }
        if (!$logsession = $this->logsessionRepository->find($id)) {
            return $this->redirectToRoute('logsession_logs', ['_locale' => $_locale, 'system' => $system]);
        }
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('logsession_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        if (!$log = $this->logRepository->find((int) $log_id)) {
            return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($log);
        $em->flush();

        $this->signalRepository->updateSignalStats($log->getSignalId(), true, true);
        $this->listenerRepository->updateListenerStats($log->getListenerId());
        if ($log->getOperatorId()) {
            $this->listenerRepository->updateListenerStats($log->getOperatorId());
        }

        if ($log->getLogSessionId()) {
            $this->listenerRepository->updateStats((int) $log->getLogSessionId());
        }

        if (!$logsession = $this->logsessionRepository->find($id)) {
            $this->session->set(
                'lastMessage',
                sprintf(
                    $this->i18n("Log entry <b>%s</b> has been deleted.<br>Log session <b>%s</b> was empty and has been removed."),
                    $log_id,
                    $id
                )
            );
            return $this->redirectToRoute('logsessions', ['_locale' => $_locale, 'system' => $system]);
        }

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->i18n("Log entry <b>%s</b> has been deleted.<br>Stats for log session <b>%s</b> have been updated."),
                $log_id,
                $id
            )
        );
        return $this->redirectToRoute('logsession_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
    }
}
