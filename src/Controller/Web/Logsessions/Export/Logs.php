<?php
namespace App\Controller\Web\Logsessions\Export;

use App\Controller\Web\Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Logsessions\Export
 */
class Logs extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{id}/logs/export/csv",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="logsession_logs_export_csv"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function csv(
        $_locale,
        $system,
        $id
    ) {
        return $this->export($_locale, $system, $id, 'csv');
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{id}/logs/export/txt",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="logsession_logs_export_txt"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function txt(
        $_locale,
        $system,
        $id
    ) {
        return $this->export($_locale, $system, $id, 'txt');
    }

    /**
     * @param $_locale
     * @param $system
     * @param $id
     * @param $mode
     * @return RedirectResponse|Response
     */
    private function export(
        $_locale,
        $system,
        $id,
        $mode
    ) {
        if (!$logsession = $this->logsessionRepository->find($id)) {
            return $this->redirectToRoute('logsession', ['system' => $system]);
        }
        $listener = $this->listenerRepository->find($logsession->getListenerId());
        $sortableColumns = $this->listenerRepository->getColumns('logs');
        $args = [
            'logSessionId' => $id,
            'sort' =>       'khz',
            'sort_2' =>     'call',
            'order' =>      'a',
        ];
        $logs = $this->logRepository->getLogs($args, $sortableColumns);
        $strlen = [
            'khz' =>        0,
            'call' =>       0,
            'lsb' =>        0,
            'usb' =>        0,
            'sec' =>        0,
            'format' =>     0,
            'pwr' =>        0,
            'dxKm' =>       0,
            'dxMiles' =>    0,
            'lat' =>        0,
            'lon' =>        0,
        ];
        foreach ($strlen as $k => $v) {
            $strlen[$k] = max(
                array_map('strlen', array_column($logs, $k))
            );
        }

        $title = strToUpper($system) . ' log session for RXX-ID ' . $listener->getId() . ' | '
            . $listener->getFormattedNameAndLocation()
            . ($listener->getMultiOperator() === 'Y' ? ' | Operator: ' . $logs[0]['operator'] : '');

        $subtitle = 'Session ' . $id . ' has ' . count($logs) . ' logs | '
            . 'Output sorted by Frequency and Callsign | '
            . 'Date: ' . date('Y-m-d');

        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              $title,
            'subtitle' =>           $subtitle,
            'system' =>             $system,
            'listener' =>           $listener,
            'logsession' =>         $logsession,
            'logs' =>               $logs,
            'strlen' =>             $strlen,
            'typeRepository' =>     $this->typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        switch ($mode) {
            case 'csv':
                $response = $this->render("logsession/export/logs.csv.twig", $parameters);
                break;
            case 'txt':
                $response = $this->render("logsession/export/logs.txt.twig", $parameters);
                break;
            default:
                die("Invalid mode");
        }
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename=logsession_{$id}_logs.{$mode}");
        return $response;
    }
}
