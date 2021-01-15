<?php
namespace App\Controller\Web\Signals;

use App\Form\Signals\Collection as Form;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Collection
 * @package App\Controller\Web
 */
class Collection extends Base
{
    private $args;
    private $columns;
    private $isAdmin;

    /**
     * @Route(
     *     "/{_locale}/{system}/signals",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param Form $form
     * @return Response
     * @throws Exception
     */
    public function controller(
        $_locale,
        $system,
        Request $request,
        Form $form
    ) {
        /* Benchmarks:
            REU - list all NDBs (no ajax):   26.6MB and 84 seconds
            REU - list all NDBs (with ajax):  6.4MB and 18 seconds
        */
        $this->isAdmin = $this->parameters['isAdmin'];
        $ajax = true;
        $this->args = [
            'admin_mode' =>     0,
            'isAdmin' =>        $this->isAdmin,
            'sortby' =>         '',
            'za' =>             '',

            'limit' =>          $this->signalRepository::defaultlimit,
            'order' =>          $this->signalRepository::defaultOrder,
            'page' =>           $this->signalRepository::defaultPage,
            'sort' =>           $this->signalRepository::defaultSorting,
            'total' =>          0,

            'active' =>         '',
            'call' =>           '',
            'channels' =>       '',
            'countries' =>      '',
            'gsq' =>            '',
            'heard_in' =>       '',
            'heard_in_mod' =>   'any',
            'khz_1' =>          '',
            'khz_2' =>          '',
            'listener' =>       [],
            'listener_invert' => '0',
            'logged_date_1' =>  '',
            'logged_date_2' =>  '',
            'logged_first_1' => '',
            'logged_first_2' => '',
            'logged_last_1' =>  '',
            'logged_last_2' =>  '',
            'offsets' =>        '',
            'paper' =>          $this->paperRepository::getDefaultForSystem($system),
            'personalise' =>    '',
            'range_gsq' =>      '',
            'range_min' =>      '',
            'range_max' =>      '',
            'range_units' =>    '',
            'recently' =>       '',
            'region' =>         $_REQUEST['form']['region'] ?? '',
            'rww_focus' =>      '',
            'show' =>           '',
            'sp_itu_clause' =>  '',
            'states' =>         '',
            'system' =>         $system,
            'type' =>           [],
            'signalTypes' =>    [0],
            'url' =>            $request->attributes->get('_route'),
            'within' =>         ''
        ];

        if (empty($request->query->all())) {
            $cookies = $request->cookies;
            if ($cookies && $cookies->has('signalsForm')) {
                parse_str($cookies->get('signalsForm'), $cookieParams);
                $this->setArgsFromRequest($cookieParams);
            }
        } else {
            $this->setArgsFromRequest($request);
        }

        foreach (['logged_date_1', 'logged_date_2', 'logged_first_1', 'logged_first_2', 'logged_last_1', 'logged_last_2'] as $arg) {
            $this->args[$arg] = $this->args[$arg] ? new DateTime($this->args[$arg]) : null;
        }
        $form = $form->buildForm($this->createFormBuilder(), $this->args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->args = $form->getData();
        }
        if (empty($this->args['type'])) {
            $this->args['type'][] = 'NDB';
        }
        if ([''] === $this->args['listener']) {
            $this->args['listener'] = [];
        }
        $this->args['isAdmin'] =      $this->isAdmin;
        $this->args['signalTypes'] =  $this->typeRepository->getSignalTypesSearched($this->args['type']);
        $paper =                isset($this->args['paper']) ? $this->paperRepository->getSpecifications($this->args['paper']) : false;
        if (in_array($this->args['show'], ['', 'list'])) {
            $signals =  [];
            $total =    0;
        } else {
            $signals =  $this->signalRepository->getFilteredSignals($system, $this->args);
            $total =    $this->signalRepository->getFilteredSignalsCount($system, $this->args);
        }
        $seeklistStats =        [];
        $seeklistColumns =      [];
        if ('seeklist' === $this->args['show']) {
            $seeklistStats =    $this->signalRepository::getSeeklistStats($signals);
            $seeklistColumns =  $this->signalRepository::getSeeklistColumns($signals, $paper);
        }
        $box =          false;
        $center =       false;
        if ('map' === $this->args['show'] && $signals) {
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
        }

        if (in_array($this->args['show'], ['map', 'json'])) {
            $highlights = [
                'call' =>       ['find' => [$this->args['call']], 'repl' => []],
                'ITU' =>        ['find' => explode(' ', $this->args['countries']), 'repl' => []],
                'GSQ' =>        ['find' => explode(' ', $this->args['gsq']), 'repl' => []],
                'heard_in_html' =>   ['find' => explode(' ', $this->args['heard_in']), 'repl' => []],
                'region' =>     ['find' => [$this->args['region']], 'repl' => []],
                'SP' =>         ['find' => explode(' ', $this->args['states']), 'repl' => []]
            ];
            foreach ($highlights as $key => &$data) {
                foreach ($data['find'] as $find) {
                    $data['repl'][] = '<em>' . $find . '</em>';
                }
            }
            foreach ($signals as &$signal) {
                foreach ($highlights as $key => $highlight) {
                    if ($highlight['find'] === '') {
                        continue;
                    }
                    $signal[$key] = str_replace(
                        $highlight['find'],
                        $highlight['repl'],
                        $signal[$key]
                    );

                }
            }
        }

        $expanded = [];
        foreach ($this->signalRepository::collapsable_sections as $section => $fields) {
            $expanded[$section] = 0;
            foreach ($fields as $field) {
                if ($this->args[$field]) {
                    $expanded[$section] = 1;
                    break;
                }
            }
        }
        $parameters = [
            '_locale' =>            $_locale,
            'ajax' =>               $ajax,
            'args' =>               $this->args,
            'box' =>                $box,
            'center' =>             $center,
            'classic' =>            $this->systemRepository->getClassicUrl('signals', $this->args['show']),
            'columns' =>            $this->signalRepository->getColumns('signals'),
            'expanded' =>           $expanded,
            'form' =>               $form->createView(),
            'isAdmin' =>            $this->isAdmin,
            'mode' =>               $this->i18n('Signals'),
            'paper' =>              $paper,
            'paperChoices' =>       $this->paperRepository->getAllChoices(),
            'personalise' =>       [
                'id' =>     ($this->args['personalise'] ?? false),
                'name' =>   ($this->args['personalise'] ?? false) ? $this->listenerRepository->getDescription($this->args['personalise']) : ''
            ],
            'results' => [
                'limit' =>              isset($this->args['limit']) ? $this->args['limit'] : $this->signalRepository::defaultlimit,
                'page' =>               isset($this->args['page']) ? $this->args['page'] : 0,
                'total' =>              $total
            ],
            'seeklistColumns' =>    $seeklistColumns,
            'seeklistStats' =>      $seeklistStats,
            'signals' =>            $signals,
            'system' =>             $system,
            'sortbyOptions' =>      $this->signalRepository->getColumns('signals'),
            'tabs' => [
                [ 'list', 'Listing' ],
                [ 'seeklist', 'Seeklist'],
                [ 'map', 'Map' ],
            ],
            'types' =>              $this->typeRepository->getAll(),
            'typeRepository' =>     $this->typeRepository
        ];
        if (isset($this->args['show']) && $this->args['show'] === 'csv') {
            $response = $this->render("signals/export/signals.csv.twig", $this->getMergedParameters($parameters));
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.csv");
            return $response;
        }
        if (isset($this->args['show']) && $this->args['show'] === 'json') {
            $title = ($this->isAdmin && $this->args['admin_mode'] === '1' ? 1 : ($this->isAdmin && $this->args['admin_mode'] === '2' ? 2 : 0));
            return $this->json([
                'args' =>           $this->args,
                'columns' =>        $this->getActiveColumns(),
                'personalise' =>    $parameters['personalise'],
                'results' =>        $parameters['results'],
                'signals' =>        $signals,
                'title' =>          $title,
                'types' =>          $this->args['signalTypes']
            ]);
        }
        if (isset($this->args['show']) && $this->args['show'] === 'kml') {
            $response = $this->render("signals/export/signals.kml.twig", $this->getMergedParameters($parameters));
            $response->headers->set('Content-Type', 'application/vnd.google-earth.kml+xml');
            $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.kml");
            return $response;
        }
        if (isset($this->args['show']) && $this->args['show'] === 'txt') {
            $response = $this->render("signals/export/signals.txt.twig", $this->getMergedParameters($parameters));
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.txt");
            return $response;
        }
        return $this->render('signals/index.html.twig', $this->getMergedParameters($parameters));
    }

    private function getActiveColumns() {
        $out = [];
        $columns  = $this->signalRepository->getColumns('signals');
        foreach ($columns as $key => $column) {
            if ($column['admin'] && !$this->isAdmin) {
                continue;
            }
            if ($column['arg'] && $this->args[$column['arg']]==='') {
                continue;
            }
            $out[] = [ $key, $column['highlight'], $column['td_class']];
        }
        return $out;
    }
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/stats",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_stats"
     * )
     * @param $system
     * @param Request $request
     * @throws Exception
     */
    public function stats(
        $system,
        Request $request
    ) {
        $args = [
            'rww_focus' =>      '',
            'system' =>         $system
        ];
        $this->setRwwFocusFromRequest($args, $request);
        $stats = $this->statsRepository->getStats();
        $out = [
            'signals' => [
                'reu' =>        (int) $stats['signals_reu'],
                'rna' =>        (int) $stats['signals_rna'],
                'rna_reu' =>    (int) $stats['signals_rna_reu'],
                'rww' =>        (int) $stats['signals_rww'],
                'unlogged' =>   (int) $stats['signals_unlogged'],
            ],
            'listeners' => [
                'focus' =>      ($args['rww_focus'] ? $this->regionRepository->getOne($args['rww_focus'])->getName() : ''),
                'locations' =>  (int) $stats['listeners_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')],
                'logs' =>       (int) $stats['logs_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')],
                'first' =>      $stats['log_first_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')],
                'last' =>       $stats['log_last_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')]
            ]
        ];
        return $this->json($out);
    }

    private function setArgsFromRequest($request)
    {
        $this->setPagingFromRequest($this->args, $request);

        $this->setTypeFromRequest($this->args, $request);
        $this->setRwwFocusFromRequest($this->args, $request);
        $this->setValueFromRequest($this->args, $request, 'call');
        $this->setPairFromRequest($this->args, $request, 'khz');
        $this->setValueFromRequest($this->args, $request, 'channels', ['1', '2']);
        $this->setValueFromRequest($this->args, $request, 'states');
        $this->setValueFromRequest($this->args, $request, 'sp_itu_clause', ['OR']);
        $this->setValueFromRequest($this->args, $request, 'countries');
        $this->setRegionFromRequest($this->args, $request);
        $this->setValueFromRequest($this->args, $request, 'gsq');
        $this->setValueFromRequest($this->args, $request, 'recently', ['logged', 'unlogged']);
        $this->setValueFromRequest($this->args, $request, 'within', array_values($this->signalRepository::withinPeriods));
        $this->setValueFromRequest($this->args, $request, 'active', ['1', '2']);

        $this->setListenersFromRequest($this->args, $request);
        $this->setValueFromRequest($this->args, $request, 'listener_invert', ['0', '1']);
        $this->setValueFromRequest($this->args, $request, 'heard_in');
        $this->setValueFromRequest($this->args, $request, 'heard_in_mod', ['any', 'all'], 'a');
        $this->setPairFromRequest($this->args, $request, 'logged_date');
        $this->setPairFromRequest($this->args, $request, 'logged_first');
        $this->setPairFromRequest($this->args, $request, 'logged_last');

        $this->setPersonaliseFromRequest($this->args, $request);
        $this->setValueFromRequest($this->args, $request, 'offsets', ['1']);
        $this->setValueFromRequest($this->args, $request, 'range_gsq');
        $this->setValueFromRequest($this->args, $request, 'range_min');
        $this->setValueFromRequest($this->args, $request, 'range_max');
        $this->setValueFromRequest($this->args, $request, 'range_units', ['km', 'mi']);
        $this->setValueFromRequest($this->args, $request, 'admin_mode', ['0', '1', '2']);

        $this->setValueFromRequest($this->args, $request, 'show', ['csv', 'json', 'kml', 'list', 'map', 'pskov', 'seeklist', 'txt'], 'a');
        $this->setValueFromRequest($this->args, $request, 'paper', ['a4', 'a4_l', 'lgl', 'lgl_l', 'ltr', 'ltr_l'], 'a');
    }
}
