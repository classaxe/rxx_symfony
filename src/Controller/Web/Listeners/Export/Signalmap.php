<?php
namespace App\Controller\Web\Listeners\Export;

use App\Controller\Web\Listeners\Base;
use App\Repository\ListenerRepository;
use App\Repository\LogRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Export
 */
class Signalmap extends Base
{
    // TODO: Remove this legacy code once the new system goes live
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
     * @param ListenerRepository $listenerRepository
     * @param LogRepository $logRepository
     * @param TypeRepository $typeRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        LogRepository $logRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            die();
        }

        $signals = $listenerRepository->getSignalsForListener($id, [ 'latlon' => true ]);
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
            $types[$s['type']] = $typeRepository->getTypeForCode($s['type']);
        }

        uasort($types, array($typeRepository, 'sortByOrder'));
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
            'logs' =>               $logRepository->getLogsForListener($id),
            'typeRepository' =>     $typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/export/signalmap.html.twig', $parameters);

        return $response;
    }
}
