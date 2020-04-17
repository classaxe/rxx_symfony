<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\Collection as Form;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Collection extends Base
{
    /**
     * @Route(
     *     "/{system}/listeners",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="_listeners"
     * )
     */
    public function _listenerListController(
        $system
    ) {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system
        ];

        return $this->redirectToRoute('listeners', $parameters, 301);
    }


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
     * @param ListenerRepository $listenerRepository
     * @param TypeRepository $typeRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function controller(
        $_locale,
        $system,
        Request $request,
        Form $form,
        ListenerRepository $listenerRepository,
        TypeRepository $typeRepository
    ) {
        $this->listenerRepository = $listenerRepository;
        $this->typeRepository = $typeRepository;

        $isAdmin = $this->parameters['isAdmin'];
        $args = [
            'isAdmin' =>    $isAdmin,
            'system' =>     $system,
            'type' =>       [],

            // Setable via GET
            'limit' =>      $listenerRepository::defaultLimit,
            'order' =>      $listenerRepository::defaultOrder,
            'page' =>       $listenerRepository::defaultPage,
            'sort' =>       $listenerRepository::defaultSorting,

            'country' =>    '',
            'has_logs' =>   '',
            'has_map_pos' => '',
            'q' =>          '',
            'region' =>     '',
            'show' =>       '',
            'timezone' =>   '',
        ];
        $this->setArgsFromRequest($args, $request);
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if (empty($args['type'])) {
            $args['type'][] = 'NDB';
        }
        $listeners =    $listenerRepository->getFilteredListeners($system, $args);
        $total =        $listenerRepository->getFilteredListenersCount($system, $args);
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
        $parameters = [
            'args' =>               $args,
            'box' =>                $box,
            'center' =>             $center,
            'classic' =>            $this->systemRepository->getClassicUrl('listeners'),
            'columns' =>            $listenerRepository->getColumns(),
            'form' =>               $form->createView(),
            'listeners' =>          $listeners,
            '_locale' =>            $_locale,
            'mode' =>               'Listeners List',
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : $listenerRepository::defaultLimit,
                'page' =>               isset($args['page']) ? $args['page'] : $listenerRepository::defaultPage,
                'total' =>              $total
            ],
            'system' =>             $system,
            'tabs' =>               $tabs
        ];
        if ($this->parameters['isAdmin']) {
            $parameters['latestListeners'] =    $listenerRepository->getLatestLoggedListeners($system);
            $parameters['latestLogs'] =         $listenerRepository->getLatestLogs($system);
        }
        return $this->render('listeners/index.html.twig', $this->getMergedParameters($parameters));
    }

    private function setArgsFromRequest(&$args, $request)
    {
        $this->setPagingFromRequest($args, $request);
        $this->setTypeFromRequest($args, $request);
        $this->setRegionFromRequest($args, $request);
        if ($args['isAdmin']) {
            $this->setValueFromRequest($args, $request, 'has_logs', ['', 'N', 'Y'], 'A');
            $this->setValueFromRequest($args, $request, 'has_map_pos', ['', 'N', 'Y'], 'A');
        }
        $this->setValueFromRequest($args, $request, 'show', ['list', 'map'], 'a');
        $this->setValueFromRequest($args, $request, 'timezone');
        $this->setValueFromRequest($args, $request, 'country', false, 'A');
        $this->setValueFromRequest($args, $request, 'q');
    }
}
