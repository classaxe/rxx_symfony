<?php
namespace App\Controller\Web\Listeners;

use App\Controller\Web\Listeners\Base;
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
     *     "/{_locale}/{system}/listeners/{id}/export",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_export"
     * )
     */
    public function exportController(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if ((int) $id) {
            $listener = $listenerRepository->find((int)$id);
            if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
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
