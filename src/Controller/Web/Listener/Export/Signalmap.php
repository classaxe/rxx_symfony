<?php
namespace App\Controller\Web\Listener\Export;

use App\Controller\Web\Listener\Base;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use App\Repository\TypeRepository;
use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Export
 */
class Signalmap extends Base
{
    /**
     * @Route(
     *     "/{system}/listeners/{id}/signalmap",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_signalmap"
     * )
     */
    public function signalmapController(
        $system,
        $id,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $listenerSignalTypes = [];
        foreach ($listenerRepository->getSignalTypesForListener($id) as $type) {
            $listenerSignalTypes[$type] = $typeRepository->getTypeForCode($type);
        }
        uasort($listenerSignalTypes, array($typeRepository, 'sortByOrder'));
        $parameters = [
            'id' =>                 $id,
            'title' =>              strToUpper($system).' Signals received by '.$listener->getName(),
            'types' =>              $listenerSignalTypes,
            'system' =>             $system,
            'listener' =>           $listener,
            'logs' =>               $logRepository->getLogsForListener($id)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/export/signalmap.html.twig', $parameters);
        return $response;
    }
}
