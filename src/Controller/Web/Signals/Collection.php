<?php
namespace App\Controller\Web\Signals;

use App\Form\Signals\Collection as Form;
use App\Repository\ListenerRepository;
use App\Repository\RegionRepository;
use App\Repository\SignalRepository;
use App\Repository\TypeRepository;
use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Collection
 * @package App\Controller\Web
 */
class Collection extends Base
{
    const defaultlimit =     50;
    const maxNoPaging =      50;
    const defaultSorting =  'khz';
    const defaultOrder =    'a';
    const pageSizes = [
        'a4' => [
            'cols' => 4,
            'lbl' => 'A4 (Portrait) - 21.6cm x 27.9cm',
            'len' => 755
        ],
        'a4_l' => [
            'cols' => 7,
            'lbl' => 'A4 (Landscape) - 27.9cm x 21.6cm',
            'len' => 470
        ],
        'lgl' => [
            'cols' => 5,
            'lbl' => 'Legal (Portrait) - 8.5" x 14"',
            'len' => 906
        ],
        'lgl_l' => [
            'cols' => 9,
            'lbl' => 'Legal (Landscape) - 14" x 8.5"',
            'len' => 490
        ],
        'ltr' => [
            'cols' => 5,
            'lbl' => 'Letter (Portrait) - 8.5" x 11"',
            'len' => 710
        ],
        'ltr_l' => [
            'cols' => 6,
            'lbl' => 'Letter (Landscape) - 11" x 8.5"',
            'len' => 490
        ]
    ];

    /**
     * @Route(
     *     "/{system}/signals",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="_signals"
     * )
     */
    public function _signalsListController(
        $system
    ) {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system
        ];

        return $this->redirectToRoute('signals', $parameters, 301);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals"
     * )
     */
    public function signalsListController(
        $_locale,
        $system,
        Request $request,
        Form $form,
        ListenerRepository $listenerRepository,
        RegionRepository $regionRepository,
        SignalRepository $signalRepository,
        TypeRepository $typeRepository
    ) {
        $args = [
            'countries' =>  '',
            'limit' =>      static::defaultlimit,
            'order' =>      static::defaultOrder,
            'page' =>       0,
            'call' =>       '',
            'channels' =>   '',
            'personalise' =>'',
            'gsq' =>        '',
            'heard_in' =>   '',
            'khz_1' =>      '',
            'khz_2' =>      '',
            'region' =>     '',
            'show' =>       'list',
            'sort' =>       static::defaultSorting,
            'sp_itu_clause' => '',
            'states' =>     '',
            'types' =>      [],
            'signalTypes' => [0]
        ];
        $options = [
            'limit' =>          static::defaultlimit,
            'maxNoPaging' =>    static::maxNoPaging,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'region' =>         (isset($_REQUEST['form']['region']) ? $_REQUEST['form']['region'] : ''),
            'system' =>         $system,
            'total' =>          $signalRepository->getFilteredSignalsCount($system, $args) // forces paging - will be made accurate later on
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if (empty($args['types'])) {
            $args['types'][] = 'type_NDB';
        }
        $args['signalTypes'] =  $typeRepository->getSignalTypesSearched($args['types']);
        $signals =              $signalRepository->getFilteredSignals($system, $args);
        $total =                $signalRepository->getFilteredSignalsCount($system, $args);

        $parameters = [
            'args' =>               $args,
            'columns' =>            $signalRepository->getColumns(),
            'form' =>               $form->createView(),
            'signals' =>            $signals,
            'options' =>            $options,
            'mode' =>               'Signals',
            '_locale' =>            $_locale,
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $total
            ],
            'statsBlocks' =>
                $signalRepository->getStats($this->isAdmin()) +
                $listenerRepository->getStats($options['system'], $options['region']),
            'system' =>             $system,
            'sortbyOptions' =>      $signalRepository->getColumns(),
            'tabs' => [
                [ 'list', 'Listing' ],
                [ 'map', 'Map' ],
                [ 'seeklist', 'Seeklist']
            ],
            'typeRepository' =>     $typeRepository
        ];
        return $this->render('signals/index.html.twig', $this->getMergedParameters($parameters));
    }
}
