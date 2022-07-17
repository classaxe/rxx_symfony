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
class RemoteLogs extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/remotelogs/export/csv",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_remotelogs_export_csv"
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
     *     "/{_locale}/{system}/listeners/{id}/remotelogs/export/txt",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_remotelogs_export_txt"
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

        $sortableColumns = $this->listenerRepository->getColumns('logs');
        $args = [
            'operatorId' => $id,
            'sort' =>       'logDate',
            'order' =>      'a',
        ];
        $logs = $this->logRepository->getLogs($args, $sortableColumns);

        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              strToUpper($system) . ' remote logs for '.$listener->getName() . " on " . date('Y-m-d'),
            'subtitle' =>           '(' . count($logs) . ' records sorted by Date and Time)',
            'system' =>             $system,
            'listener' =>           $listener,
            'logs' =>               $logs,
            'typeRepository' =>     $this->typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        switch ($mode) {
            case 'csv':
                $response = $this->render("listener/export/remotelogs.csv.twig", $parameters);
                break;
            case 'txt':
                $response = $this->render("listener/export/remotelogs.txt.twig", $parameters);
                break;
            default:
                die("Invalid mode");
        }
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename=listener_{$id}_remotelogs.{$mode}");
        return $response;
    }
}
