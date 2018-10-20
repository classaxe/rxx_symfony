<?php
namespace App\Controller\Web;

use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerExport extends Base
{

    /**
     * @Route(
     *     "/{system}/listeners/{id}/export",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_export"
     * )
     */
    public function listenerExportController(
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if ((int) $id) {
            $listener = $listenerRepository->find((int)$id);
            if (!$listener) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }

        $parameters = [
            'id' =>                 $id,
            'mode' =>               'Export Loggings for '.$listener->getFormattedNameAndLocation(),
            'logs' =>               $listener->getCountLogs(),
            'signals' =>            $listener->getCountSignals(),
            'popupNdbWeblog' =>     "status=1,scrollbars=1,resizable=1",
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/export.html.twig', $parameters);
    }
}
