<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\Collection as Form;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Collection extends Base
{
    private $filename;
    private $system;

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listeners"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param Form $form
     * @return Response
     */
    public function controller(
        $_locale,
        $system,
        Request $request,
        Form $form
    ) {
        $this->system =     $system;
        $isAdmin = $this->parameters['isAdmin'];
        $args = [
            'isAdmin' =>    $isAdmin,
            'system' =>     $system,
            'type' =>       [],

            // Setable via GET
            'limit' =>      $this->listenerRepository::defaultLimit,
            'order' =>      $this->listenerRepository::defaultOrder,
            'page' =>       $this->listenerRepository::defaultPage,
            'sort' =>       $this->listenerRepository::defaultSorting,

            'rxx_id' =>     '',
            'multiop' =>    '',
            'primary' =>    '',
            'status' =>     '',
            'country' =>    '',
            'equipment' =>  '',
            'has_logs' =>   '',
            'has_map_pos' => '',
            'notes' =>      '',
            'q' =>          '',
            'region' =>     '',
            'show' =>       '',
            'timezone' =>   'ALL',
        ];
        if (empty($request->query->all())) {
            $cookies = $request->cookies;
            if ($cookies && $cookies->has('listenersForm')) {
                parse_str($cookies->get('listenersForm'), $cookieParams);
                $this->setArgsFromRequest($args, $cookieParams, false);
            }
        } else {
            $this->setArgsFromRequest($args, $request);
        }

        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if (empty($args['type'])) {
            $args['type'][] = 'NDB';
        }
        $listeners =    $this->listenerRepository->getFilteredListeners($system, $args);
        $total =        $this->listenerRepository->getFilteredListenersCount($system, $args);
        if (empty($listeners)) {
            $args['show'] = 'list';
        }
        $box =          false;
        $center =       false;
        if ('map' === $args['show'] && $listeners) {
            $lats =     array_column($listeners, 'lat');
            $lons =     array_column($listeners, 'lon');
            $lat_min =  min($lats);
            $lat_max =  max($lats);
            $lon_min =  min($lons);
            $lon_max =  max($lons);
            $lat_cen =  $lat_min + (($lat_max - $lat_min) / 2);
            $lon_cen =  $lon_min + (($lon_max - $lon_min) / 2);
            $box =      [[$lat_min, $lon_min], [$lat_max, $lon_max]];
            $center =   [$lat_cen, $lon_cen];
        }

        $tabs =[[ 'list', 'Listing', '&#x1F5D2;' ]];
        foreach ($listeners as $l) {
            if ($l->getSignalsMap()) {
                $tabs[] = [ 'map', 'Map', '&#x1F310;' ];
                break;
            }
        }
        $width = [
            'narrow' => 640,
            'medium' => 1035 + (22 * count($args['type'])) + ($isAdmin ? 300 : 0)
        ];
        $parameters = [
            'args' =>               $args,
            'box' =>                $box,
            'center' =>             $center,
            'columns' =>            $this->listenerRepository->getColumns('listeners'),
            'form' =>               $form->createView(),
            'listeners' =>          $listeners,
            '_locale' =>            $_locale,
            'mode' =>               'Listeners and Locations',
            'results' => [
                'limit' =>          $args['limit'],
                'page' =>           $args['page'],
                'total' =>          $total
            ],
            'system' =>             $system,
            'tabs' =>               $tabs,
            'width' =>              $width
        ];
        if ($this->parameters['isAdmin']) {
            $parameters['latestListeners'] =    $this->listenerRepository->getLatestLoggedListeners($system);
            $parameters['latestLogs'] =         $this->listenerRepository->getLatestLogs($system);
        }
        if (isset($args['show'])) {
            switch($args['show']) {
                case 'csv':
                    return $this->renderCsv($parameters);
                case 'kml':
                    return $this->renderKml($parameters);
                case 'txt':
                    return $this->renderTxt($parameters);
            }
        }

        return $this->render('listeners/index.html.twig', $this->getMergedParameters($parameters));
    }

    private function renderCsv($parameters)
    {
        $filename = ($this->filename ?: $this->system .'_listeners.csv');
        $response = $this->render("listeners/export/listeners.csv.twig", $this->getMergedParameters($parameters));
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename={$filename}");
        return $response;
    }

    private function renderKml($parameters)
    {
        $filename = ($this->filename ?: $this->system .'_listeners.kml');
        $response = $this->render("listeners/export/listeners.kml.twig", $this->getMergedParameters($parameters));
        $response->headers->set('Content-Type', 'application/vnd.google-earth.kml+xml');
        $response->headers->set('Content-Disposition',"attachment;filename={$filename}");
        return $response;
    }

    private function renderTxt($parameters)
    {
        $filename = ($this->filename ?: $this->system .'_listeners.txt');
        $response = $this->render("listeners/export/listeners.txt.twig", $this->getMergedParameters($parameters));
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename={$filename}");
        return $response;
    }

    private function setArgsFromRequest(&$args, $request, $withPageNumber = true)
    {
        $this->setPagingFromRequest($args, $request, $withPageNumber);
        $this->setValueFromRequest($args, $request, 'show', ['csv', 'list', 'map'], 'a');

        $this->setValueFromRequest($args, $request, 'q');
        $this->setTypeFromRequest($args, $request);

        $this->setRegionFromRequest($args, $request);
        $this->setValueFromRequest($args, $request, 'country', false, 'A');

        $this->setTimezoneFromRequest($args, $request);

        $this->setValueFromRequest($args, $request, 'status', ['', 'N', 'Y', '1D', '5D', '10D', '20D', '30D', '3M', '6M', '1Y', '2Y', '5Y'], 'A');
        $this->setValueFromRequest($args, $request, 'loctype', ['Y', 'N'], 'A');
        $this->setValueFromRequest($args, $request, 'multiop', ['Y', 'N'], 'A');
        $this->setValueFromRequest($args, $request, 'equipment');
        $this->setValueFromRequest($args, $request, 'rxx_id');
        $this->setValueFromRequest($args, $request, 'notes');
        if ($args['isAdmin']) {
            $this->setValueFromRequest($args, $request, 'has_map_pos', ['', 'N', 'Y'], 'A');
            $this->setValueFromRequest($args, $request, 'has_logs', ['', 'N', 'Y'], 'A');
        }
        $this->setValueFromRequest($args, $request, 'filename');
    }
}
