<?php
namespace App\Controller\Web\Logsessions\Export;

use App\Controller\Web\Listeners\Base;

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
            'sort' =>       'logDate',
            'order' =>      'a',
        ];
        $logs = $this->logRepository->getLogs($args, $sortableColumns);

        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              strToUpper($system) . ' logs for Log session '.$id . ' for ' . $listener->getFormattedNameAndLocation(),
            'subtitle' =>           '(' . count($logs) . ' records sorted by Date and Time)',
            'system' =>             $system,
            'listener' =>           $listener,
            'logsession' =>         $logsession,
            'logs' =>               $logs,
            'typeRepository' =>     $this->typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        switch ($mode) {
            case 'csv':
                $response = $this->render("listener/export/logs.csv.twig", $parameters);
                break;
            case 'txt':
                $response = $this->render("listener/export/logs.txt.twig", $parameters);
                break;
            default:
                die("Invalid mode");
        }
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename=logsession_{$id}_logs.{$mode}");
        return $response;
    }
}
