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
class Signalmap extends Base
{
    // Exists only to support legacy listener maps
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/signalmap",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_signalmap"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id
    ) {
        if (!$listener = $this->getValidReportingListener($id)) {
            die();
        }

        $signals = $this->listenerRepository->getSignalsForListener($id, [ 'latlon' => true ]);
        // Don't bother sorting by anything - no list is shown in this mode

        $lats =     array_column($signals, 'lat');
        $lons =     array_column($signals, 'lon');
        $lat_min =  min($lats);
        $lat_max =  max($lats);
        $lon_min =  min($lons);
        $lon_max =  max($lons);
        $lat_cen =  $lat_min + (($lat_max - $lat_min) / 2);
        $lon_cen =  $lon_min + (($lon_max - $lon_min) / 2);
        $box =      [[$lat_min, $lon_min], [$lat_max, $lon_max]];
        $center =   [$lat_cen, $lon_cen];
        $types = [];
        foreach ($signals as $s) {
            $types[$s['type']] = $this->typeRepository->getTypeForCode($s['type']);
        }

        uasort($types, [ $this->typeRepository, 'sortByOrder' ]);
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'box' =>                $box,
            'center' =>             $center,
            'title' =>              strToUpper($system).' Signals received by '.$listener->getName(),
            'types' =>              $types,
            'signals' =>            $signals,
            'system' =>             $system,
            'listener' =>           $listener,
            'logs' =>               $this->logRepository->getLogsForListener($id),
            'typeRepository' =>     $this->typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('listener/export/signalmap.html.twig', $parameters);
    }
}
