<?php
namespace App\Controller\Web\Listener\Export;

use App\Controller\Web\Listener\Base;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Export
 */
class Logs extends Base
{
    /**
     * @Route(
     *     "/{system}/listeners/{id}/export/logs",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_logs"
     * )
     */
    public function logsController(
        $system,
        $id,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $logs = $logRepository->getLogsForListener($id);
        $parameters = [
            'title' =>              strToUpper($system) . ' log for '.$listener->getName() . " on " . date('Y-m-d'),
            'subtitle' =>           '(' . count($logs) . ' records sorted by Date and Time)',
            'system' =>             $system,
            'listener' =>           $listener,
            'logs' =>               $logs
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/export/logs.txt.twig', $parameters);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
}
