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
     *     "/{_locale}/{system}/stats/listeners/count/{region}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listeners_count"
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
     *     name="logs_count"
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
     *     name="logs_first"
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
        $dates =        $this->logRepository->getFirstAndLastLog($system, $region);
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
     *     name="logs_last"
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
        $dates =        $this->logRepository->getFirstAndLastLog($system, $region);
        $textResponse = new Response($dates[ 'last' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/signals/count/rna",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_count_rna"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function signals_count_rna (
        $_locale,
        $system
    ) {
        $stats =        $this->signalRepository->getStats();
        $textResponse = new Response($stats[ 'signals' ][ 'RNA Only' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/signals/count/reu",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_count_reu"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function signals_count_reu (
        $_locale,
        $system
    ) {
        $stats =        $this->signalRepository->getStats();
        $textResponse = new Response($stats[ 'signals' ][ 'REU Only' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/signals/count/rna_reu",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_count_rna_reu"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function signals_count_rna_reu (
        $_locale,
        $system
    ) {
        $stats =        $this->signalRepository->getStats();
        $textResponse = new Response($stats[ 'signals' ][ 'RNA + REU' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/signals/count/rww",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_count_rww"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function signals_count_rww (
        $_locale,
        $system
    ) {
        $stats =        $this->signalRepository->getStats();
        $textResponse = new Response($stats[ 'signals' ][ 'RWW' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/signals/count/unlogged",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_count_unlogged"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function signals_count_unlogged (
        $_locale,
        $system
    ) {
        $stats =        $this->signalRepository->getStats();
        $textResponse = new Response($stats[ 'signals' ][ 'Unlogged' ] , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }
}
