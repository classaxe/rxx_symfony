<?php
namespace App\Controller\Web\Listeners\Export;

use App\Controller\Web\Listeners\Base;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Export
 */
class Logs extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/export/logs",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_logs"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository
     * @param LogRepository $logRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute(
                'listeners',
                ['system' => $system]
            );
        }
        $logs = $logRepository->getLogsForListener($id);
        $parameters = [
            '_locale' =>            $_locale,
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
