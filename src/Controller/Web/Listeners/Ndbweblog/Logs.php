<?php
namespace App\Controller\Web\Listeners\Ndbweblog;

use App\Controller\Web\Listeners\Base;
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
     *     "/{_locale}/{system}/listeners/{id}/ndbweblog/logs.js",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_logs"
     * )
     */
    public function logsController(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            '_locale' =>            $_locale,
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
