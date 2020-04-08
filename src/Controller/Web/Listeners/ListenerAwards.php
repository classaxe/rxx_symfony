<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerAward as ListenerAwardForm;
use App\Repository\AwardRepository;
use App\Repository\ListenerRepository;
use App\Repository\SystemRepository;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport_EsmtpTransport;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerAwards extends Base
{
    private $awardRepository;
    private $listener;
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
     * @param Request $request
     * @param AwardRepository $awardRepository
     * @param ListenerAwardForm $listenerAwardForm
     * @param ListenerRepository $listenerRepository
     * @param Swift_Mailer $mailer
     * @param SystemRepository $systemRepositiory
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $filter,
        Request $request,
        AwardRepository $awardRepository,
        ListenerAwardForm $listenerAwardForm,
        ListenerRepository $listenerRepository,
        Swift_Mailer $mailer,
        SystemRepository $systemRepository
    ) {
        $this->awardRepository = $awardRepository;
        $this->listenerRepository = $listenerRepository;
        if ((int) $id) {
            if (!$this->listener = $this->getValidReportingListener($id, $this->listenerRepository)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }
        $options = [
            'email' =>  $this->listener->getEmail(),
            'id' =>     $this->listener->getId(),
            'name' =>   $this->listener->getName(),
        ];
        $form = $listenerAwardForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            $admin = $systemRepository::AWARDS[0];
            $cc =    $systemRepository::AUTHORS[0];
            $html =  $this->renderView('emails/admin/award.html.twig', [
                'admin' => $admin['name'] . ' ' . $admin['role'],
                'awards' => explode(',', $form_data['awards']),
                'filter' => $form_data['filter'],
                'id' => $this->listener->getId(),
                'name' => $this->listener->getName(),
                'system' => $system
            ]);
            $text = $this->renderView('emails/admin/award.txt.twig', [
                'admin' => $admin['name'] . ' ' . $admin['role'],
                'awards' => explode(',', $form_data['awards']),
                'filter' => $form_data['filter'],
                'id' => $this->listener->getId(),
                'name' => $this->listener->getName(),
                'system' => $system
            ]);
            $message = (new Swift_Message('NDB LIST AWARD REQUEST'))
                ->setReplyTo( [ $form_data['email'] =>  $this->listener->getName() ] )
                ->setFrom('rxx@classaxe.com')
                ->setTo( [ $admin['email'] => $admin['name'] . ' ' . $admin['role']])
                ->setCc( [ $cc['email'] => $cc['name'] ])
                ->setBody($html,'text/html')
                ->addPart($text,'text/plain');

            $transport = $mailer->getTransport();
            if ($transport instanceof Swift_Transport_EsmtpTransport){
                $transport->setStreamOptions([
                    'ssl' => ['allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false]
                ]);
            }
            $mailer->send($message);
        }

        $this->signals = $this->listenerRepository->getLogsForListener(
            $this->listener->getId(),
            [ 'type' => 0, 'sort' => 'khz', 'order' => 'a']
        );

        $isAdmin = $this->parameters['isAdmin'];
        $award_types = array_keys(AwardRepository::AWARDSPEC);
        $awards = [];
        foreach ($award_types as $type) {
            if ('*' !== $filter) {
                $types = explode(',', $filter);
                $base_types = [];
                foreach ($types as $t) {
                    $base_types[] = explode('-', $t)[0];
                }
                if (!in_array($type, $base_types)) {
                    continue;
                }
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
                case 'transatlantic':
                case 'transpacific':
                    $awards[$type] = $this->getTransoceanic($type);
                    break;
                case 'transcontinental':
                    $awards[$type] = $this->getTranscontinental($type);
                    break;
            }
        }

        $daytime = $listenerRepository->getDaytimeHours($this->listener->getTimezone());
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               'Awards Available for '.$this->listener->getFormattedNameAndLocation(),
            'award_types' =>        $award_types,
            'awards' =>             $awards,
            'daytime_start' =>      $daytime['start'],
            'daytime_end' =>        $daytime['end'],
            'form' =>               $form->createView(),
            'l' =>                  $this->listener,
            'repo' =>               $listenerRepository,
            'logs' =>               $this->listener->getCountLogs(),
            'message' =>            $html ?? '',
            'signals' =>            $this->listener->getCountSignals(),
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($this->listener, $isAdmin)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/awards/awards.html.twig', $parameters);
    }

    /**
     * @param string $award
     * @return array|bool
     */
    private function getBestDx(string $award)
    {
        $ranges = $this->awardRepository->getAwardSpec($award);
        $result = [];
        foreach ($ranges as $range) {
            $key = implode('|', $range);
            $result[$key] = [];
        }

        $valid = false;
        foreach ($this->signals as $signal) {
            if ('daytime' === $award && !$signal['daytime']) {
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
            return false;
        }
        return $result;
    }

    /**
     * @param string $award
     * @return array|bool
     */
    private function getContinentalDx(string $award)
    {
        $ranges = $this->awardRepository->getAwardSpec($award);
        $region = explode('_', $award)[1];
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

        if (!$places) {
            return false;
        }

        $places = array_keys($places);
        sort($places);
        $offset = 0;
        $result = [ 'total' => count($places) ];
        foreach ($ranges as $range) {
            $result[$range] = [];
            for ($i = 0; $i < $range - $offset; $i++) {
                if (count($places)) {
                    $result[$range][] = array_shift($places);
                }
            }
            $offset = $range;
        }
        return $result;
    }

    /**
     * @param string $award
     * @return array|bool
     */
    private function getCountryDx(string $award)
    {
        $spec = $this->awardRepository->getAwardSpec($award);
        $filtered = [];
        foreach ($this->signals as $s) {
            if (!in_array($s['itu'], $spec['ITU'])) {
                continue;
            }
            $filtered[$s['khz'].'-'.$s['call']] = $s;
        }

        if (!$filtered) {
            return false;
        }

        $result = [ 'total' => count($filtered) ];
        foreach ($spec['QTY'] as $range) {
            $result[$range] = [];
        }

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

    /**
     * @param string $award
     * @return array|bool
     */
    private function getNorth60(string $award)
    {
        $spec = $this->awardRepository->getAwardSpec($award);
        $filtered = [];
        foreach ($this->signals as $s) {
            if ($s['lat'] < 60) {
                continue;
            }
            $filtered[$s['khz'].'-'.$s['call']] = $s;
        }

        if (!$filtered) {
            return false;
        }

        $result = [ 'total' => count($filtered) ];
        $offset = 0;
        foreach ($spec as $range) {
            $result[$range] = [];
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

    /**
     * @param string $award
     * @return array|bool
     */
    private function getSingle(string $award)
    {
        $spec = $this->awardRepository->getAwardSpec($award);
        foreach ($this->signals as $s) {
            if ($s['call'] === $spec['call'] && (float)$s['khz'] === (float)$spec['khz']) {
                return $s;
            }
        }
        return false;
    }

    /**
     * @param string $award
     * @return array|bool
     */
    private function getTranscontinental(string $award)
    {
        $spec = $this->awardRepository->getAwardSpec($award);

        $result = [];

        $e = [];
        $w = [];
        foreach ($this->signals as $s) {
            if (in_array($s['sp'], $spec['SP_E'])) {
                $e[$s['khz'].'-'.$s['call']] = $s;
                continue;
            }
            if (in_array($s['sp'], $spec['SP_W'])) {
                $w[$s['khz'].'-'.$s['call']] = $s;
                continue;
            }
        }
        $result['total'] = min(count($e), count($w));

        $offset = 0;
        foreach ($spec['QTY'] as $range) {
            $result[$range] = [];
            $taken = count($result[$range]);
            for ($i = 0; $i < $range - $offset - $taken; $i++) {
                if (count($e) && count($w)) {
                    $_e = array_shift($e);
                    $_e['required'] = false;
                    $_w = array_shift($w);
                    $_w['required'] = false;
                    $result[$range]['e'][] = $_e;
                    $result[$range]['w'][] = $_w;
                }
            }
            $offset = $range;
        }
//        print "<pre>" . print_r($result, true) . "</pre>"; die;
        return $result;
    }

    /**
     * @param string $award
     * @return array|bool
     */
    private function getTransoceanic(string $award)
    {
        $specs = $this->awardRepository->getAwardSpec($award);
        $spec = false;
        foreach (array_keys($specs) as $key) {
            $key_arr = explode(',', $key);
            if (in_array($this->listener->getRegion(), $key_arr) || in_array($this->listener->getItu(), $key_arr)) {
                $spec = $specs[$key];
            }
        }
        if (!$spec) {
            return false;
        }

        $result = [];

        $filtered = [];
        foreach ($this->signals as $s) {
            if (!in_array($s['region'], $spec['LOC']) && !in_array($s['itu'], $spec['LOC'])) {
                continue;
            }
            $filtered[$s['khz'].'-'.$s['call']] = $s;
        }
        $result['total'] = count($filtered);
        $offset = 0;
        foreach ($spec['QTY'] as $range) {
            $result[$range] = [];
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
}
