<?php
namespace App\Controller\Web\Listeners;

use App\Controller\Web\Base;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerDelete extends Base
{
    /**
     * @Route(
     *     "/{locale}/{system}/listeners/{id}/delete",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_delete"
     * )
     */
    public function deleteController(
        $locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if (!(int) $id) {
            return $this->redirectToRoute('listeners', ['locale' => $locale, 'system' => $system]);
        }
        $listener = $listenerRepository->find((int) $id);
        if (!$listener) {
            return $this->redirectToRoute('listeners', ['locale' => $locale, 'system' => $system]);
        }
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('listener', ['locale' => $locale, 'system' => $system, 'id' => $id]);
        }
        if ($listener->getCountLogs() > 0) {
            $this->session->set(
                'lastError',
                "Listener ".$listener->getName()." has ".$listener->getCountLogs()." logs and cannot be deleted"
            );
            return $this->redirectToRoute('listeners', ['locale' => $locale, 'system' => $system]);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($listener);
        $em->flush();

        $this->session->set('lastMessage', "Listener ".$listener->getName()." has been deleted");
        return $this->redirectToRoute('listeners', ['locale' => $locale, 'system' => $system]);
    }
}
