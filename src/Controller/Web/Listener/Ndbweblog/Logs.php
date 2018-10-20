<?php
namespace App\Controller\Web\Listener\Ndbweblog;

use App\Controller\Web\Listener\Base;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Ndbweblog
 */
class Logs extends Base
{
    /**
     * @Route(
     *     "/{system}/listeners/{id}/ndbweblog/logs.js",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_logs"
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
        $parameters = [
            'title' =>              'NDB Weblog logs for '.$listener->getName(),
            'system' =>             $system,
            'listener' =>           $listener,
            'logs' =>               $logRepository->getLogsForListener($id)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/ndbweblog/logs.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Content-Disposition','attachment;filename=logs.js');
        return $response;
    }
}
