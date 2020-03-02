<?php
namespace App\Controller\Web\Listeners;

use App\Repository\CleRepository;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerAwards extends Base
{

    private $cleRepository;
    private $listenerID;
    private $listenerRepository;
    private $signals;

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/awards",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_awards"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param ListenerRepository $listenerRepository
     * @param CleRepository $cleRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        CleRepository $cleRepository
    ) {

        if ((int) $id) {
            if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }

        $this->listenerID = (int) $id;
        $this->cleRepository = $cleRepository;
        $this->listenerRepository = $listenerRepository;
        $this->signals = $this->listenerRepository->getLogsForListener($this->listenerID, [ 'type' => 0] );

        $isAdmin = $this->parameters['isAdmin'];


        $awards = [
            'daytime' =>    $this->getBestDx('DAYTIME'),
            'longranger' => $this->getBestDx('LONGRANGER'),
            'region_eu' =>  $this->getRegionDx('eu'),
            'region_na' =>  $this->getRegionDx('na'),
            'region_ca' =>  $this->getRegionDx('ca'),
            'region_sa' =>  $this->getRegionDx('sa'),
            'region_af' =>  $this->getRegionDx('af'),
            'region_as' =>  $this->getRegionDx('as'),
            'region_oc' =>  $this->getRegionDx('oc'),
            'region_an' =>  $this->getRegionDx('an'),
        ];

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               'Awards Available for '.$listener->getFormattedNameAndLocation(),
            'awards' =>             $awards,
            'daytime_start' =>      str_pad((1000 + $listener->getTimezone() * 100 % 2400), 4, '0'),
            'daytime_end' =>        str_pad((1400 + $listener->getTimezone() * 100 % 2400), 4, '0'),
            'l' =>                  $listener,
            'repo' =>               $listenerRepository,
            'logs' =>               $listener->getCountLogs(),
            'signals' =>            $listener->getCountSignals(),
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener, $isAdmin)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/awards/awards.html.twig', $parameters);
    }

    private function getBestDx($award)
    {
        $ranges = $this->cleRepository->getAwardSpec($award);
        $result = [];
        foreach ($ranges as $range) {
            $key = implode('|', $range);
            $result[$key] = [];
        }
        $valid = false;
        foreach ($this->signals as $signal) {
            if ('DAYTIME' === $award && !$signal['daytime']) {
                continue;
            }
            foreach ($ranges as $range) {
                $key = implode('|', $range);
                if ($signal['dxMiles'] >= $range[0] && (0 === $range[1] || $signal['dxMiles'] <= $range[1])) {
                    if (!$result[$key] || $result[$key]['dxMiles'] <= $signal['dxMiles']) {
                        $valid = true;
                        $result[$key] = $signal;
                    }
                }
            }
        }
        if (!$valid) {
            return [];
        }
        return $result;
    }

    private function getRegionDx($region)
    {
        $ranges = $this->cleRepository->getAwardSpec('REGION_' . strtoupper($region));
        $result = [ 'total' => 0 ];
        foreach ($ranges as $range) {
            $result[$range] = [];
        }
        $places = [];
        foreach ($this->signals as $signal) {
            if ($signal['region'] !== $region) {
                continue;
            }
            switch ($signal['place']) {
                case "HI":
                    $place = 'HWA';
                    break;
                case "PR":
                    $place = 'PTR';
                    break;
                default:
                    $place = $signal['place'];
                    break;
            }
            $places[$place] = true;
        }
        $places = array_keys($places);
        sort($places);
        $offset = 0;
        $result['total'] = count($places);
        foreach ($ranges as $range) {
            for ($i = 0; $i < $range - $offset; $i++) {
                if (count($places)) {
                    $result[$range][] = array_shift($places);
                }
            }
            $offset = $range;
        }
        return $result;
    }
}
