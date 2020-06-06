<?php
namespace App\Controller\Web\Listeners;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerLogDelete extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logs/{log_id}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_log_delete"
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
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }
        $listener = $this->listenerRepository->find((int) $id);
        if (!$listener) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }

        $log = $this->logRepository->find((int) $log_id);
        if (!$log) {
            return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($log);
        $em->flush();

        $this->signalRepository->updateSignalStats($log->getSignalId(), true, true);
        $this->listenerRepository->updateListenerStats($log->getListenerId());

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->i18n("Log entry has been deleted. Stats for %s have been updated."),
                $listener->getName()
            )
        );
        return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
    }
}
