<?php
namespace App\Controller\Web\Listeners;

use App\Repository\AwardRepository;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerAwards extends Base
{

    private $awardRepository;
    private $listenerID;
    private $listenerRepository;
    private $signals;

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/awards/{filter}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"="*"},
     *     name="listener_awards"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param $filter
     * @param ListenerRepository $listenerRepository
     * @param AwardRepository $cleRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $filter,
        ListenerRepository $listenerRepository,
        AwardRepository $awardRepository
    ) {
        if ((int) $id) {
            if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }

        $this->listenerID = (int) $id;
        $this->awardRepository = $awardRepository;
        $this->listenerRepository = $listenerRepository;
        $this->signals = $this->listenerRepository->getLogsForListener($this->listenerID, [ 'type' => 0, 'sort' => 'khz', 'order' => 'a']);

        $isAdmin = $this->parameters['isAdmin'];
        $award_types = array_keys(AwardRepository::AWARDSPEC);
        $awards = [];
        foreach ($award_types as $type) {
            if ('*' !== $filter && !in_array($type, explode(',', $filter))) {
                continue;
            }
            $family = explode('_', $type)[0];
            switch($family) {
                case 'continental':
                    $awards[$type] = $this->getContinentalDx($type);
                    break;
                case 'country':
                    $awards[$type] = $this->getCountryDx($type);
                    break;
                case 'daytime':
                case 'longranger':
                    $awards[$type] = $this->getBestDx($type);
                    break;
                case 'lt':
                    $awards[$type] = $this->getSingle($type);
                    break;
                case 'north60':
                    $awards[$type] = $this->getNorth60($type);
                    break;
            }
        }

        $daytime = $listenerRepository->getDaytimeHours($listener->getTimezone());
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               'Awards Available for '.$listener->getFormattedNameAndLocation(),
            'award_types' =>        $award_types,
            'awards' =>             $awards,
            'daytime_start' =>      $daytime['start'],
            'daytime_end' =>        $daytime['end'],
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
        $ranges = $this->awardRepository->getAwardSpec($award);
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

    private function getContinentalDx($award)
    {
        $ranges = $this->awardRepository->getAwardSpec($award);
        $region = explode('_', $award)[1];
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

    private function getCountryDx($award)
    {
        $spec = $this->awardRepository->getAwardSpec($award);
        $result = [ 'total' => 0 ];
        foreach ($spec['QTY'] as $range) {
            $result[$range] = [];
        }
        $filtered = [];
        foreach ($this->signals as $s) {
            if (!in_array($s['itu'], $spec['ITU'])) {
                continue;
            }
            $filtered[$s['khz'].'-'.$s['call']] = $s;
        }
        $result['total'] = count($filtered);

        if ($spec['ALL']) {
            foreach ($spec['QTY'] as $range) {
                $required = $spec['ITU'];
                foreach ($required as $r) {
                    foreach ($filtered as $f) {
                        if ($f['itu'] === $r) {
                            $f['required'] = true;
                            $result[$range][] = $f;
                            $required = array_diff($required, [$f['itu']]);
                            break;
                        }
                    }
                }
                if ($required === []) {
                    foreach ($result[$range] as $r) {
                        unset ($r['required']);
                        unset($filtered[array_search($r, $filtered)]);
                    }
                }
                if ($result['total'] < $range) {
                    break;
                }
            }
        }

        $offset = 0;
        foreach ($spec['QTY'] as $range) {
            $taken = count($result[$range]);
            for ($i = 0; $i < $range - $offset - $taken; $i++) {
                if (count($filtered)) {
                    $f = array_shift($filtered);
                    $f['required'] = false;
                    $result[$range][] = $f;
                }
            }
            $offset = $range;
        }
        return $result;
    }

    private function getNorth60($award)
    {
        $spec = $this->awardRepository->getAwardSpec($award);
        $result = [ 'total' => 10 ];
        foreach ($spec as $range) {
            $result[$range] = [];
        }
        $filtered = [];
        foreach ($this->signals as $s) {
            if ($s['lat'] < 60) {
                continue;
            }
            $filtered[$s['khz'].'-'.$s['call']] = $s;
        }
        $result['total'] = count($filtered);
        $offset = 0;
        foreach ($spec as $range) {
            $taken = count($result[$range]);
            for ($i = 0; $i < $range - $offset - $taken; $i++) {
                if (count($filtered)) {
                    $f = array_shift($filtered);
                    $f['required'] = false;
                    $result[$range][] = $f;
                }
            }
            $offset = $range;
        }
        return $result;
    }

    private function getSingle($award)
    {
        $spec = $this->awardRepository->getAwardSpec($award);
        foreach ($this->signals as $s) {
            if ($s['call'] === $spec['call'] && (float)$s['khz'] === (float)$spec['khz']) {
                return $s;
            }
        }
        return false;
    }

}
