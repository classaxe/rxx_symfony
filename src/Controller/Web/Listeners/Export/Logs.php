<?php
namespace App\Controller\Web\Listeners\Export;

use App\Controller\Web\Listeners\Base;

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
     *     "/{_locale}/{system}/listeners/{id}/logs/export/csv",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_logs_export_csv"
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
     *     "/{_locale}/{system}/listeners/{id}/logs/export/txt",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_logs_export_txt"
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
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute(
                'listeners',
                ['system' => $system]
            );
        }
        $logs = $this->logRepository->getLogsForListener($id);
        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              strToUpper($system) . ' log for '.$listener->getName() . " on " . date('Y-m-d'),
            'subtitle' =>           '(' . count($logs) . ' records sorted by Date and Time)',
            'system' =>             $system,
            'listener' =>           $listener,
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
        }
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename=listener_{$id}_logs.{$mode}");
        return $response;
    }

}
