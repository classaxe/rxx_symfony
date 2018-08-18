<?php
namespace App\Controller;

use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller
 */
class ListenerExport extends Base
{
    /**
     * @Route(
     *     "/{system}/listener/{id}/export/ndbweblog",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_ndbweblog"
     * )
     */
    public function listenerExportNdbWeblogController(
        $system,
        $id,
        ListenerRepository $listenerRepo
    ) {
        if (!(int) $id) {
            $this->session->set('lastError', "Listener cannot be found.");
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $listener = $listenerRepo->find((int) $id);
        if (!$listener) {
            $this->session->set('lastError', "Listener cannot be found");
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        if (!$listener->getCountLogs()) {
            $this->session->set('lastError', "Listener <strong>".$listener->getName()."</strong> has no logs to view.");
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            'id' =>                 $id,
            'mode' =>               'NDB Weblog for '.$listener->getName(),
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/export/ndbweblog.html.twig', $parameters);
    }
}
