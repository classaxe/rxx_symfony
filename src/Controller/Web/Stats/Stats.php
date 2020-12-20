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
     *     "/{_locale}/{system}/stats",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="stats"
     * )
     * @param $system
     * @param $region
     * @return Response
     */
    public function stats(
        $system
    ) {
        $stats = $this->statsRepository->getStats();
        $out = json_encode($stats);
        $textResponse = new Response($out , 200);
        $textResponse->headers->set('Content-Type', 'application/json');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/listeners/{region}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "region": "af|an|as|ca|eu|iw|na|oc|sa|xx|all"
     *     },
     *     name="stats_listeners"
     * )
     * @param $system
     * @param $region
     * @return Response
     */
    public function listeners(
        $system,
        $region = ''
    ) {
        $stats = $this->statsRepository->getStats();
        $result = [
            'count' => ($region ?
                ($stats['listeners_' . $system . '_' . $region] ?? '?') : ($stats['listeners_' . $system] ?? '?')
            )
        ];
        $out = json_encode($result);
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
     * @param $system
     * @param $region
     * @return Response
     */
    public function listeners_count(
        $system,
        $region = ''
    ) {
        $stats = $this->statsRepository->getStats();
        $count = ($region ?
            ($stats['listeners_' . $system . '_' . $region] ?? '?') : ($stats['listeners_' . $system] ?? '?')
        );
        $textResponse = new Response($count , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/logs/count/{region}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "region": "af|an|as|ca|eu|iw|na|oc|sa|xx"
     *     },
     *     name="stats_logs_count"
     * )
     * @param $system
     * @param $region
     * @return Response
     */
    public function logs_count(
        $system,
        $region = ''
    ) {
        $stats = $this->statsRepository->getStats();
        $count = ($region ? ($stats['logs_' . $system . '_' . $region] ?? '?') : ($stats['logs_' . $system] ?? '?'));
        $textResponse = new Response($count , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/logs/first/{region}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "region": "af|an|as|ca|eu|iw|na|oc|sa|xx"
     *     },
     *     name="stats_logs_first"
     * )
     * @param $system
     * @param $region
     * @return Response
     */
    public function logs_first(
        $system,
        $region = ''
    ) {
        $stats = $this->statsRepository->getStats();
        $date = ($region ? ($stats['log_first_' . $system . '_' . $region] ?? '?') : ($stats['log_first_' . $system] ?? '?'));
        $textResponse = new Response($date , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/logs/last/{region}",
     *     requirements={
     *        "system": "reu|rna|rww",
     *        "region": "af|an|as|ca|eu|iw|na|oc|sa|xx"
     *     },
     *     name="stats_logs_last"
     * )
     * @param $system
     * @param $region
     * @return Response
     */
    public function logs_last(
        $system,
        $region = ''
    ) {
        $stats = $this->statsRepository->getStats();
        $date = ($region ? ($stats['log_last_' . $system . '_' . $region] ?? '?') : ($stats['log_last_' . $system] ?? '?'));

        $textResponse = new Response($date , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/stats/signals/count/{region}",
     *     requirements={
     *        "system": "reu|rna|reu_rww|rww|unlogged",
     *        "region": "af|an|as|ca|eu|iw|na|oc|sa|xx"
     *     },
     *     name="stats_signals_count"
     * )
     * @param $system
     * @param string $region
     * @return Response
     */
    public function signals_count (
        $system,
        $region = ''
    ) {
        $stats = $this->statsRepository->getStats();
        $count = ($region ? ($stats['signals_' . $system . '_' . $region] ?? '?') : ($stats['signals_' . $system] ?? '?'));
        $textResponse = new Response($count , 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        return $textResponse;
    }
}
