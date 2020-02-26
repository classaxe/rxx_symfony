<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\Collection as Form;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Collection extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'name';
    const defaultOrder =    'a';

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
     */
    public function controller(
        $_locale,
        $system,
        Request $request,
        Form $form,
        ListenerRepository $listenerRepository
    ) {
        $args = [
            'country' =>    '',
            'dx_gsq' =>     '',
            'dx_min' =>     '',
            'dx_max' =>     '',
            'dx_units' =>   'km',
            'filter' =>     '',
            'limit' =>      static::defaultlimit,
            'order' =>      static::defaultOrder,
            'page' =>       0,
            'region' =>     $_REQUEST['form']['region'] ?? '',
            'show' =>       '',
            'sort' =>       static::defaultSorting,
            'system' =>     $system,
            'types' =>      [],
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if (empty($args['types'])) {
            $args['types'][] = 'type_NDB';
        }
        $listeners =    $listenerRepository->getFilteredListeners($system, $args);
        $total =        $listenerRepository->getFilteredListenersCount($system, $args);

        $box =          false;
        $center =       false;
        if ($listeners) {
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

        $parameters = [
            'args' =>               $args,
            'box' =>                $box,
            'center' =>             $center,
            'columns' =>            $listenerRepository->getColumns(),
            'form' =>               $form->createView(),
            'listeners' =>          $listeners,
            '_locale' =>            $_locale,
            'mode' =>               'Listeners List',
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $total
            ],
            'system' =>             $system,
            'tabs' => [
                [ 'list', 'Listing' ],
                [ 'map', 'Map' ],
            ]
        ];
        if ($this->parameters['isAdmin']) {
            $parameters['latestListeners'] =    $listenerRepository->getLatestLoggedListeners($system);
            $parameters['latestLogs'] =         $listenerRepository->getLatestLogs($system);
        }
        return $this->render('listeners/index.html.twig', $this->getMergedParameters($parameters));
    }
}
