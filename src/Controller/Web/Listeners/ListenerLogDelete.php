<?php
namespace App\Controller\Web\Listeners;

use App\Controller\Web\Base;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
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
     * @param ListenerRepository $listenerRepository
     * @param LogRepository $logRepository
     * @return RedirectResponse
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $log_id,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository
    ) {
        if (!(int) $id) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }
        $listener = $listenerRepository->find((int) $id);
        if (!$listener) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }

        $log = $logRepository->find((int) $log_id);
        if (!$log) {
            return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($log);
        $em->flush();

        $listenerRepository->updateLogCountsForListener((int) $id);

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->translator->trans("Log entry has been deleted. Listener log counts have been updated."),
                $listener->getName()
            )
        );
        return $this->redirectToRoute('listener_logs', ['_locale' => $_locale, 'system' => $system, 'id' => $id]);
    }
}
