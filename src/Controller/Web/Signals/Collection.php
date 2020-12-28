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
        $isAdmin = $this->parameters['isAdmin'];
        $ajax = true;
        $args = [
            'admin_mode' =>     0,
            'isAdmin' =>        $isAdmin,
            'sortby' =>         '',
            'za' =>             '',

            'limit' =>          $this->signalRepository::defaultlimit,
            'order' =>          $this->signalRepository::defaultOrder,
            'page' =>           $this->signalRepository::defaultPage,
            'sort' =>           $this->signalRepository::defaultSorting,

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

        $this->setArgsFromRequest($args, $request);

        foreach (['logged_date_1', 'logged_date_2', 'logged_first_1', 'logged_first_2', 'logged_last_1', 'logged_last_2'] as $arg) {
            $args[$arg] = $args[$arg] ? new DateTime($args[$arg]) : null;
        }
        $args['total'] =        $this->signalRepository->getFilteredSignalsCount($system, $args); // forces paging - will be made accurate later on
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if (empty($args['type'])) {
            $args['type'][] = 'NDB';
        }
        if ([''] === $args['listener']) {
            $args['listener'] = [];
        }
        $args['isAdmin'] =      $isAdmin;
        $args['signalTypes'] =  $this->typeRepository->getSignalTypesSearched($args['type']);
        $paper =                isset($args['paper']) ? $this->paperRepository->getSpecifications($args['paper']) : false;
        $signals =              $this->signalRepository->getFilteredSignals($system, $args);
        $total =                $this->signalRepository->getFilteredSignalsCount($system, $args);
        $seeklistStats =        [];
        $seeklistColumns =      [];
        if ('seeklist' === $args['show']) {
            $seeklistStats =    $this->signalRepository::getSeeklistStats($signals);
            $seeklistColumns =  $this->signalRepository::getSeeklistColumns($signals, $paper);
        }
        $box =          false;
        $center =       false;
        if ('map' === $args['show'] && $signals) {
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
        foreach ($signals as $signal) {
            if ('seeklist' !== $args['show']) {
                $signal['first_heard'] =    $signal['first_heard'] ? new DateTime($signal['first_heard']) : null;
                $signal['last_heard'] =     $signal['last_heard'] ? new DateTime($signal['last_heard']) : null;
            }
        }

        $expanded = [];
        foreach ($this->signalRepository::collapsable_sections as $section => $fields) {
            $expanded[$section] = 0;
            foreach ($fields as $field) {
                if ($args[$field]) {
                    $expanded[$section] = 1;
                    break;
                }
            }
        }
        $parameters = [
            '_locale' =>            $_locale,
            'ajax' =>               $ajax,
            'args' =>               $args,
            'box' =>                $box,
            'center' =>             $center,
            'classic' =>            $this->systemRepository->getClassicUrl('signals', $args['show']),
            'columns' =>            $this->signalRepository->getColumns('signals'),
            'expanded' =>           $expanded,
            'form' =>               $form->createView(),
            'isAdmin' =>            $isAdmin,
            'mode' =>               $this->i18n('Signals'),
            'paper' =>              $paper,
            'paperChoices' =>       $this->paperRepository->getAllChoices(),
            'personalised' =>       isset($args['personalise']) ? $this->listenerRepository->getDescription($args['personalise']) : false,
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : $this->signalRepository::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
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
        if (isset($args['show']) && $args['show'] === 'csv') {
            $response = $this->render("signals/export/signals.csv.twig", $this->getMergedParameters($parameters));
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.csv");
            return $response;
        }
        if (isset($args['show']) && $args['show'] === 'json') {
            return $this->json([
                'results' =>    $parameters['results'],
                'signals' =>    $signals,
                'types' =>      $args['signalTypes']
            ]);
        }
        if (isset($args['show']) && $args['show'] === 'kml') {
            $response = $this->render("signals/export/signals.kml.twig", $this->getMergedParameters($parameters));
            $response->headers->set('Content-Type', 'application/vnd.google-earth.kml+xml');
            $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.kml");
            return $response;
        }
        if (isset($args['show']) && $args['show'] === 'txt') {
            $response = $this->render("signals/export/signals.txt.twig", $this->getMergedParameters($parameters));
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.txt");
            return $response;
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
                'focus' =>      ($args['rww_focus'] ? $this->regionRepository->get($args['rww_focus'])->getName() : ''),
                'locations' =>  (int) $stats['listeners_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')],
                'logs' =>       (int) $stats['logs_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')],
                'first' =>      $stats['log_first_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')],
                'last' =>       $stats['log_last_' . $system . ($args['rww_focus'] ? '_' . $args['rww_focus'] : '')]
            ]
        ];
        return $this->json($out);
    }

    private function setArgsFromRequest(&$args, $request)
    {
        $this->setPagingFromRequest($args, $request);

        $this->setTypeFromRequest($args, $request);
        $this->setRwwFocusFromRequest($args, $request);
        $this->setValueFromRequest($args, $request, 'call');
        $this->setPairFromRequest($args, $request, 'khz');
        $this->setValueFromRequest($args, $request, 'channels', ['1', '2']);
        $this->setValueFromRequest($args, $request, 'states');
        $this->setValueFromRequest($args, $request, 'sp_itu_clause', ['OR']);
        $this->setValueFromRequest($args, $request, 'countries');
        $this->setRegionFromRequest($args, $request);
        $this->setValueFromRequest($args, $request, 'gsq');
        $this->setValueFromRequest($args, $request, 'recently', ['logged', 'unlogged']);
        $this->setValueFromRequest($args, $request, 'within', array_values($this->signalRepository::withinPeriods));
        $this->setValueFromRequest($args, $request, 'active', ['1', '2']);

        $this->setListenersFromRequest($args, $request);
        $this->setValueFromRequest($args, $request, 'listener_invert', ['0', '1']);
        $this->setValueFromRequest($args, $request, 'heard_in');
        $this->setValueFromRequest($args, $request, 'heard_in_mod', ['any', 'all'], 'a');
        $this->setPairFromRequest($args, $request, 'logged_date');
        $this->setPairFromRequest($args, $request, 'logged_first');
        $this->setPairFromRequest($args, $request, 'logged_last');

        $this->setPersonaliseFromRequest($args, $request);
        $this->setValueFromRequest($args, $request, 'offsets', ['1']);
        $this->setValueFromRequest($args, $request, 'range_gsq');
        $this->setValueFromRequest($args, $request, 'range_min');
        $this->setValueFromRequest($args, $request, 'range_max');
        $this->setValueFromRequest($args, $request, 'range_units', ['km', 'mi']);
        $this->setValueFromRequest($args, $request, 'admin_mode', ['0', '1', '2']);

        $this->setValueFromRequest($args, $request, 'show', ['csv', 'json', 'kml', 'list', 'map', 'pskov', 'seeklist', 'txt'], 'a');
        $this->setValueFromRequest($args, $request, 'paper', ['a4', 'a4_l', 'lgl', 'lgl_l', 'ltr', 'ltr_l'], 'a');
    }
}
