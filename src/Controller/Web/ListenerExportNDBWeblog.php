<?php
namespace App\Controller\Web;

use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use App\Repository\SignalRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerExportNDBWeblog extends Base
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
    public function baseController(
        $system,
        $id,
        ListenerRepository $listenerRepo
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepo)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            'id' =>                 $id,
            'title' =>              'NDB Weblog for '.$listener->getName(),
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/export/ndbweblog/base.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/listener/{id}/export/ndbweblog/config.js",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_ndbweblog_config"
     * )
     */
    public function configController(
        $system,
        $id,
        ListenerRepository $listenerRepo
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepo)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            'title' => 'NDB Weblog config for ' . $listener->getName(),
            'system' => $system,
            'listener' => $listener
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/export/ndbweblog/config.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        return $response;
    }

    /**
     * @Route(
     *     "/{system}/listener/{id}/export/ndbweblog/logs.js",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_ndbweblog_logs"
     * )
     */
    public function logsController(
        $system,
        $id,
        ListenerRepository $listenerRepo,
        LogRepository $logRepo
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepo)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            'title' =>              'NDB Weblog logs for '.$listener->getName(),
            'system' =>             $system,
            'listener' =>           $listener,
            'logs' =>               $logRepo->getLogsForListener($id)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/export/ndbweblog/logs.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        return $response;
    }

    /**
     * @Route(
     *     "/{system}/listener/{id}/export/ndbweblog/stations.js",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_ndbweblog_stations"
     * )
     */
    public function stationsController(
        $system,
        $id,
        ListenerRepository $listenerRepo,
        SignalRepository $signalRepo
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepo)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            'title' =>              'NDB Weblog stations for '.$listener->getName(),
            'system' =>             $system,
            'listener' =>           $listener,
            'signals' =>            $signalRepo->getSignalsForListener($id)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/export/ndbweblog/stations.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        return $response;
    }

    private function getValidListener($id, $listenerRepo)
    {
        if (!(int) $id) {
            $this->session->set('lastError', "Listener cannot be found.");
            return false;
        }
        $listener = $listenerRepo->find((int) $id);
        if (!$listener) {
            $this->session->set('lastError', "Listener cannot be found");
            return false;
        }
        if (!$listener->getCountLogs()) {
            $this->session->set('lastError', "Listener <strong>".$listener->getName()."</strong> has no logs to view.");
            return false;
        }
        return $listener;
    }
}
