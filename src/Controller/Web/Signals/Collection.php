<?php
namespace App\Controller\Web\Signals;

use App\Form\Signals\Collection as Form;
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
    const defaultlimit =     100;
    const maxNoPaging =      100;
    const defaultSorting =  'khz';
    const defaultOrder =    'a';
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
        SignalRepository $signalRepository,
        TypeRepository $typeRepository
    ) {
        $args = [
            'country' =>    '',
            'limit' =>      static::defaultlimit,
            'order' =>      static::defaultOrder,
            'page' =>       0,
            'region' =>     '',
            'sort' =>       static::defaultSorting,
            'types' =>      [],
            'signalTypes' => [0]
        ];
        $options = [
            'limit' =>          static::defaultlimit,
            'maxNoPaging' =>    static::maxNoPaging,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'system' =>         $system,
            'region' =>         (isset($_REQUEST['form']['region']) ? $_REQUEST['form']['region'] : ''),
            'total' =>          $signalRepository->getFilteredSignalsCount($system, $args)
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if (empty($args['types'])) {
            $args['types'][] = 'type_NDB';
        }
        $args['signalTypes'] = $typeRepository->getSignalTypesSearched($args['types']);
        $signals =      $signalRepository->getFilteredSignals($system, $args);
        $total =        $signalRepository->getFilteredSignalsCount($system, $args);

        $parameters = [
            'args' =>               $args,
            'columns' =>            $signalRepository->getColumns(),
            'form' =>               $form->createView(),
            'signals' =>            $signals,
            'matched' =>            ($options['total'] > $options['maxNoPaging'] ? 'of ' : 'all ') . $total . ' signals',
            'mode' =>               'Signals List',
            'listenerPopup' =>      'width=800,height=680,status=1,scrollbars=1,resizable=1',
            '_locale' =>            $_locale,
            'system' =>             $system,
            'text' =>
                "<ul>\n"
                ."    <li>Log and station counts are updated each time new log data is added - "
                ."figures are for logs in the system at this time.</li>\n"
                ."    <li>To see stats for different types of signals, check the boxes shown for 'Types' below.</li>\n"
                ."    <li>This report prints best in Portrait.</li>\n"
                ."</ul>\n",
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $total
            ]
        ];
        return $this->render('signals/index.html.twig', $this->getMergedParameters($parameters));
    }
}
