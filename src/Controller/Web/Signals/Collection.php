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
    private $expandedSections = [];
    private $filename;
    private $isAdmin;
    private $mapBox =           [];
    private $mapCenter =        [];
    private $pageLayout =       [];
    private $personalise =      [
        'id' =>     null,
        'desc' =>   null,
        'name' =>   null,
        'lat' =>    null,
        'lon' =>    null,
        'qth' =>    null,
        'sp' =>     null,
        'itu' =>    null,
    ];
    private $request;
    private $seeklistData =     [];
    private $seeklistStats =    [];
    private $signals =          [];
    private $system;
    private $total =            false;

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
    public function signals(
        $_locale,
        $system,
        Request $request,
        Form $form
    ) {
        $this->system =     $system;
        $this->isAdmin =    $this->parameters['isAdmin'];
        $this->request =    $request;

        $this->setArgsInitial();
        $this->setArgsFromCookieOrUrl();
        $this->setArgsConvertDates();

        $form = $form->buildForm($this->createFormBuilder(), $this->args);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->args = $form->getData();
        }
        $this->setArgsAfterPostTweaks();
        $this->setPersonalise();
        $this->args['source'] = $this->request->isXmlHttpRequest() ? 'xmlhttp' : 'page';
        $this->filename =       $this->args['filename'] ?? false;
        if ($this->request->isXmlHttpRequest() || !in_array($this->args['show'], ['list'])) {
            $this->signals =    $this->signalRepository->getFilteredSignals($this->system, $this->args);
            $this->total =      $this->signalRepository->getFilteredSignalsCount($this->system, $this->args);
        }
        $this->setMapViewport();
        $this->setHighlighting();
        $this->setExpandedSections();
        $this->setSeeklistFormattedLayout();
        $parameters = [
            '_locale' =>            $_locale,
            'args' =>               $this->args,
            'box' =>                $this->mapBox,
            'center' =>             $this->mapCenter,
            'columns' =>            $this->signalRepository->getColumns('signals'),
            'expanded' =>           $this->expandedSections,
            'form' =>               $form->createView(),
            'isAdmin' =>            $this->isAdmin,
            'mode' =>               $this->i18n('Signals'),
            'pageLayout' =>         $this->pageLayout,
            'paperChoices' =>       $this->paperRepository->getAllChoices(),
            'personalise' =>        $this->personalise,
            'results' => [
                'limit' =>              isset($this->args['limit']) ? $this->args['limit'] : $this->signalRepository::defaultlimit,
                'page' =>               isset($this->args['page']) ? $this->args['page'] : 0,
                'total' =>              (int) $this->total
            ],
            'seeklistData' =>       $this->seeklistData,
            'seeklistStats' =>      $this->seeklistStats,
            'show' => [
                'dx' =>          ($this->args['range_gsq'] ? true : false),
                'personalise' => ($this->personalise['id'] ? true : false)
            ],
            'signals' =>            $this->signals,
            'system' =>             $this->system,
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
            return $this->renderCsv($parameters);
        }
        if (isset($this->args['show']) && $this->args['show'] === 'kml') {
            return $this->renderKml($parameters);
        }
        if (isset($this->args['show']) && $this->args['show'] === 'txt') {
            return $this->renderTxt($parameters);
        }
        if ($this->request->isXmlHttpRequest() && isset($this->args['show']) && $this->args['show'] === 'list') {
            return $this->renderJsonList($parameters);
        }
        return $this->render('signals/index.html.twig', $this->getMergedParameters($parameters));
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

    private function renderCsv($parameters)
    {
        $filename = ($this->filename ? $this->filename : $this->system .'_signals.csv');
        $response = $this->render("signals/export/signals.csv.twig", $this->getMergedParameters($parameters));
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename={$filename}");
        return $response;
    }

    private function renderJsonList($parameters)
    {
        $title = ($this->isAdmin && $this->args['admin_mode'] === '1' ?
            1 : ($this->isAdmin && $this->args['admin_mode'] === '2' ? 2 : 0)
        );
        return $this->json([
            'args' =>           $this->args,
            'columns' =>        $this->setActiveColumns(),
            'personalise' =>    $parameters['personalise'],
            'results' =>        $parameters['results'],
            'signals' =>        $this->signals,
            'title' =>          $title,
            'types' =>          $this->args['signalTypes']
        ]);
    }

    private function renderKml($parameters)
    {
        $filename = ($this->filename ? $this->filename : $this->system .'_signals.kml');
        $response = $this->render("signals/export/signals.kml.twig", $this->getMergedParameters($parameters));
        $response->headers->set('Content-Type', 'application/vnd.google-earth.kml+xml');
        $response->headers->set('Content-Disposition',"attachment;filename={$filename}");
        return $response;
    }

    private function renderTxt($parameters)
    {
        $filename = ($this->filename ? $this->filename : $this->system .'_signals.txt');
        $response = $this->render("signals/export/signals.txt.twig", $this->getMergedParameters($parameters));
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename={$filename}");
        return $response;
    }

    private function setActiveColumns() {
        $out = [];
        $columns  = $this->signalRepository->getColumns('signals');
        foreach ($columns as $key => $column) {
            if ($column['admin'] && !$this->isAdmin) {
                continue;
            }
            if ($column['arg'] && $this->args[$column['arg']]==='') {
                continue;
            }
            $out[] = [
                'key' =>        $key,
                'label' =>      $column['label'],
                'order' =>      $column['order'],
                'sort' =>       $column['sort'],
                'td_class' =>   $column['td_class'],
                'th_class' =>   $column['th_class'],
                'tooltip' =>    $column['tooltip'],
            ];
        }
        return $out;
    }

    private function setArgsAfterPostTweaks()
    {
        if (empty($this->args['type'])) {
            $this->args['type'][] = 'NDB';
        }
        $this->args['isAdmin'] =      $this->isAdmin;
        $this->args['signalTypes'] =  $this->typeRepository->getSignalTypesSearched($this->args['type']);
    }

    private function setArgsConvertDates()
    {
        foreach ($this->signalRepository::dateFields as $arg) {
            $this->args[$arg] = $this->args[$arg] ? new DateTime($this->args[$arg]) : null;
        }
    }

    private function setArgsFromCookieOrUrl()
    {
        if (empty($this->request->query->all())) {
            $cookies = $this->request->cookies;
            if ($cookies && $cookies->has('signalsForm')) {
                parse_str($cookies->get('signalsForm'), $cookieParams);
                $this->setArgsFromRequest($cookieParams, false);
            }
        } else {
            $this->setArgsFromRequest($this->request);
        }
    }

    private function setArgsInitial()
    {
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
            'filename' =>       '',
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
            'morse' =>          '',
            'hidenotes' =>      '',
            'offsets' =>        '',
            'paper' =>          $this->paperRepository::getDefaultForSystem($this->system),
            'personalise' =>    '',
            'range_gsq' =>      '',
            'range_min' =>      '',
            'range_max' =>      '',
            'range_units' =>    '',
            'recently' =>       '',
            'region' =>         '',
            'rww_focus' =>      '',
            'show' =>           '',
            'sp_itu_clause' =>  '',
            'states' =>         '',
            'system' =>         $this->system,
            'type' =>           [],
            'signalTypes' =>    [0],
            'url' =>            $this->request->attributes->get('_route'),
            'within' =>         ''
        ];
    }

    private function setExpandedSections()
    {
        foreach ($this->signalRepository::collapsable_sections as $section => $fields) {
            $this->expandedSections[$section] = 0;
            foreach ($fields as $field) {
                if ($this->args[$field]) {
                    $this->expandedSections[$section] = 1;
                    break;
                }
            }
        }
    }

    private function setHighlighting()
    {
        if (!in_array($this->args['show'], ['list', 'map'])) {
            return;
        }
        if (in_array($this->args['show'], ['list']) && !$this->request->isXmlHttpRequest()) {
            return;
        }
        $highlights = [
            'call' =>           ['find' => [$this->args['call']], 'repl' => []],
            'ITU' =>            ['find' => explode(' ', $this->args['countries']), 'repl' => []],
            'GSQ' =>            ['find' => explode(' ', $this->args['gsq']), 'repl' => []],
            'heard_in_html' =>  ['find' => explode(' ', $this->args['heard_in']), 'repl' => []],
            'region' =>         ['find' => [$this->args['region']], 'repl' => []],
            'SP' =>             ['find' => explode(' ', $this->args['states']), 'repl' => []]
        ];
        foreach ($highlights as $key => &$data) {
            foreach ($data['find'] as $find) {
                $data['repl'][] = '<em>' . $find . '</em>';
            }
        }
        foreach ($this->signals as &$signal) {
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

    private function setMapViewport()
    {
        if (!$this->signals || $this->args['show'] !== 'map') {
            return;
        }
        $lats =             array_column($this->signals, 'lat');
        $lons =             array_column($this->signals, 'lon');
        $lat_min =          min($lats);
        $lat_max =          max($lats);
        $lon_min =          min($lons);
        $lon_max =          max($lons);
        $lat_cen =          $lat_min + (($lat_max - $lat_min) / 2);
        $lon_cen =          $lon_min + (($lon_max - $lon_min) / 2);
        $this->mapBox =     [[$lat_min, $lon_min], [$lat_max, $lon_max]];
        $this->mapCenter =  [$lat_cen, $lon_cen];
    }

    private function setPersonalise()
    {
        if (!$this->args['personalise'] ?? false) {
            return;
        }
        $personalise = $this->listenerRepository->find($this->args['personalise']);
        $this->personalise = [
            'id' =>     ($personalise ? $personalise->getId() : ''),
            'desc' =>   ($personalise ? $personalise->getFormattedNameAndLocation() : ''),
            'name' =>   ($personalise ? $personalise->getName() : ''),
            'lat' =>    ($personalise ? $personalise->getLat() : ''),
            'lon' =>    ($personalise ? $personalise->getLon() : ''),
            'qth' =>    ($personalise ? $personalise->getQth() : ''),
            'sp' =>     ($personalise ? $personalise->getSp() : ''),
            'itu' =>    ($personalise ? $personalise->getItu() : ''),
        ];

    }

    private function setSeeklistFormattedLayout()
    {
        if ('seeklist' !== $this->args['show']) {
            return;
        }
        $this->pageLayout =     isset($this->args['paper']) ? $this->paperRepository->getSpecifications($this->args['paper']) : false;
        $this->seeklistStats =  $this->signalRepository::getSeeklistStats($this->signals);
        $this->seeklistData =   $this->signalRepository::getSeeklistTabulatedData($this->signals, $this->pageLayout);
    }

    private function setArgsFromRequest($r, $withPageNumber = true)
    {
        $this->setPagingFromRequest($this->args, $r, $withPageNumber);

        $this->setTypeFromRequest($this->args, $r);
        $this->setRwwFocusFromRequest($this->args, $r);
        $this->setValueFromRequest($this->args, $r, 'call');
        $this->setPairFromRequest($this->args, $r, 'khz');
        $this->setValueFromRequest($this->args, $r, 'channels', ['1', '2', '3', '4']);
        $this->setValueFromRequest($this->args, $r, 'states');
        $this->setValueFromRequest($this->args, $r, 'sp_itu_clause', ['OR']);
        $this->setValueFromRequest($this->args, $r, 'countries');
        $this->setRegionFromRequest($this->args, $r);
        $this->setValueFromRequest($this->args, $r, 'gsq');
        $this->setValueFromRequest($this->args, $r, 'recently', ['logged', 'unlogged']);
        $this->setValueFromRequest($this->args, $r, 'within', array_values($this->signalRepository::withinPeriods));
        $this->setValueFromRequest($this->args, $r, 'active', ['1', '2']);

        $this->setListenersFromRequest($this->args, $r);
        $this->setValueFromRequest($this->args, $r, 'listener_invert', ['0', '1']);
        $this->setValueFromRequest($this->args, $r, 'heard_in');
        $this->setValueFromRequest($this->args, $r, 'heard_in_mod', ['any', 'all'], 'a');
        $this->setPairFromRequest($this->args, $r, 'logged_date');
        $this->setPairFromRequest($this->args, $r, 'logged_first');
        $this->setPairFromRequest($this->args, $r, 'logged_last');

        $this->setPersonaliseFromRequest($this->args, $r);
        $this->setValueFromRequest($this->args, $r, 'offsets', ['', '1']);
        $this->setValueFromRequest($this->args, $r, 'morse', ['', '1']);
        $this->setValueFromRequest($this->args, $r, 'hidenotes', ['', '1']);
        $this->setValueFromRequest($this->args, $r, 'range_gsq');
        $this->setValueFromRequest($this->args, $r, 'range_min');
        $this->setValueFromRequest($this->args, $r, 'range_max');
        $this->setValueFromRequest($this->args, $r, 'range_units', ['km', 'mi']);
        $this->setValueFromRequest($this->args, $r, 'admin_mode', ['0', '1', '2']);

        $this->setValueFromRequest($this->args, $r, 'show', ['csv', 'kml', 'list', 'map', 'pskov', 'seeklist', 'txt'], 'a');
        $this->setValueFromRequest($this->args, $r, 'paper', ['a4', 'a4_l', 'lgl', 'lgl_l', 'ltr', 'ltr_l'], 'a');
        $this->setValueFromRequest($this->args, $r, 'filename');
    }
}
