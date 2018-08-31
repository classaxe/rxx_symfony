<?php
namespace App\Controller\Web;

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
     *     "/{system}/listener/{id}/delete",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_delete"
     * )
     */
    public function listenerDeleteController(
        $system,
        $id,
        ListenerRepository $listenerRepo
    ) {
        if (!(int) $id) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $listener = $listenerRepo->find((int) $id);
        if (!$listener) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }
        if ($listener->getCountLogs() > 0) {
            $this->session->set(
                'lastError',
                "Listener ".$listener->getName()." has ".$listener->getCountLogs()." logs and cannot be deleted"
            );
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($listener);
        $em->flush();

        $this->session->set('lastMessage', "Listener ".$listener->getName()." has been deleted");
        return $this->redirectToRoute('listeners', ['system' => $system]);
    }
}
