<?php
namespace App\Controller\Web\Signals;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class SignalLogDelete extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/logs/{log_id}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_log_delete"
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
            return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system]);
        }
        $signal = $this->signalRepository->find((int) $id);
        if (!$signal) {
            return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system]);
        }

        $log = $this->logRepository->find((int) $log_id);
        if (!$log) {
            return $this->redirectToRoute('signal_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('signal_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($log);
        $em->flush();

        $this->signalRepository->updateSignalStats($log->getSignalId(), true, true);
        $this->listenerRepository->updateListenerStats($log->getListenerId());
        if ($log->getOperatorId()) {
            $this->listenerRepository->updateListenerStats($log->getOperatorId());
        }

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->i18n("Log entry has been deleted. Stats for %s have been updated."),
                $signal->getFormattedIdent()
            )
        );
        return $this->redirectToRoute('signal_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
    }
}
