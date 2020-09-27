<?php
namespace App\Controller\Web\Stats;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Logs
 * @package App\Controller\Web
 */
class Stats extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/stats/listeners/{region}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "region": "af|an|as|ca|eu|iw|na|oc|sa|xx|all"
     *     },
     *     name="stats_listeners"
     * )
     * @param $_locale
     * @param $system
     * @param $region
     * @return Response
     */
    public function listeners(
        $_locale,
        $system,
        $region = ''
    ) {
        if ($region) {
            $results = [];
            $params = ($region === 'all' ? [] : [ 'region' => $region ]);
            $results[$region] = [
                'count' => $this->listenerRepository->getFilteredListenersCount($system, $params)
            ];
            $out = json_encode($results);
        } else {
            $results = [];
            foreach(explode('|', 'af|an|as|ca|eu|iw|na|oc|sa|xx|all') as $region) {
                $params = ($region === 'all' ? [] : [ 'region' => $region ]);
                $results[$region] = [
                    'count' => $this->listenerRepository->getFilteredListenersCount($system, $params)
                ];
            }
            $out = json_encode($results);
        }
        $textResponse = new Response($out , 200);
        $textResponse->headers->set('Content-Type', 'application/json');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/listeners/count/{region}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "region": "af|an|as|ca|eu|iw|na|oc|sa|xx"
     *     },
     *     name="stats_listeners_count"
     * )
     * @param $_locale
     * @param $system
     * @param $region
     * @return Response
     */
    public function listeners_count(
        $_locale,
        $system,
        $region = ''
    ) {
        $count =        $this->listenerRepository->getFilteredListenersCount($system, [ 'region' => $region ]);
        $textResponse = new Response($count , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/logs/count/{region}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="stats_logs_count"
     * )
     * @param $_locale
     * @param $system
     * @param $region
     * @return Response
     */
    public function logs_count(
        $_locale,
        $system,
        $region = ''
    ) {
        $count =        $this->logRepository->getFilteredLogsCount($system, $region);
        $textResponse = new Response($count , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/logs/first/{region}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="stats_logs_first"
     * )
     * @param $_locale
     * @param $system
     * @param $region
     * @return Response
     */
    public function logs_first(
        $_locale,
        $system,
        $region = ''
    ) {
        $dates =        $this->listenerRepository->getFirstAndLastLog($system, $region);
        $textResponse = new Response($dates[ 'first' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/logs/last/{region}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="stats_logs_last"
     * )
     * @param $_locale
     * @param $system
     * @param $region
     * @return Response
     */
    public function logs_last(
        $_locale,
        $system,
        $region = ''
    ) {
        $dates =        $this->listenerRepository->getFirstAndLastLog($system, $region);
        $textResponse = new Response($dates[ 'last' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/signals/count/{sys}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "sys": "reu|rna|rna_reu|rww|unlogged"
     *     },
     *     name="stats_signals_count"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function signals_count (
        $_locale,
        $system,
        $sys
    ) {
        $stats =        $this->signalRepository->getStats();
        $textResponse = new Response($stats[ 'signals' ][ $sys ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }
}
