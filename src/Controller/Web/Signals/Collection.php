<?php
namespace App\Controller\Web\Signals;

use App\Entity\Signal as SignalEntity;
use App\Form\Signals\Collection as Form;
use App\Repository\ListenerRepository;
use App\Repository\PaperRepository;
use App\Repository\SignalRepository;
use App\Repository\TypeRepository;
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
     * @param ListenerRepository $listenerRepository
     * @param SignalRepository $signalRepository
     * @param TypeRepository $typeRepository
     * @return Response
     * @throws Exception
     */
    public function controller(
        $_locale,
        $system,
        Request $request,
        Form $form,
        PaperRepository $paperRepository,
        ListenerRepository $listenerRepository,
        SignalRepository $signalRepository,
        TypeRepository $typeRepository
    ) {
        $isAdmin = $this->parameters['isAdmin'];
        $args = [
            'active' =>         '',
            'admin_mode' =>     $_REQUEST['form']['admin_mode'] ?? 0,
            'call' =>           '',
            'channels' =>       '',
            'countries' =>      '',
            'gsq' =>            '',
            'heard_in' =>       '',
            'heard_in_mod' =>   'any',
            'isAdmin' =>        $isAdmin,
            'khz_1' =>          '',
            'khz_2' =>          '',
            'limit' =>          SignalRepository::defaultlimit,
            'listener' =>       [],
            'listener_invert' => '0',
            'logged_date_1' =>  '',
            'logged_date_2' =>  '',
            'logged_first_1' => '',
            'logged_first_2' => '',
            'logged_last_1' =>  '',
            'logged_last_2' =>  '',
            'offsets' =>        '',
            'order' =>          SignalRepository::defaultOrder,
            'page' =>           0,
            'paper' =>          $paperRepository::getDefaultForSystem($system),
            'personalise' =>    '',
            'range_gsq' =>      '',
            'range_min' =>      '',
            'range_max' =>      '',
            'range_units' =>    '',
            'region' =>         $_REQUEST['form']['region'] ?? '',
            'rww_focus' =>      '',
            'show' =>           '',
            'sort' =>           SignalRepository::defaultSorting,
            'sortby' =>         '',
            'sp_itu_clause' =>  '',
            'states' =>         '',
            'system' =>         $system,
            'type' =>           [],
            'signalTypes' =>    [0],
            'url' =>            $request->attributes->get('_route'),
            'za' =>             ''
        ];

        $this->setArgsFromRequest($args, $request);

        if (empty($args['type'])) {
            $args['type'][] = 'NDB';
        }
        foreach (['logged_date_1', 'logged_date_2', 'logged_first_1', 'logged_first_2', 'logged_last_1', 'logged_last_2'] as $arg) {
            $args[$arg] = $args[$arg] ? new DateTime($args[$arg]) : null;
        }
        $args['total'] =        $signalRepository->getFilteredSignalsCount($system, $args); // forces paging - will be made accurate later on
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if ([''] === $args['listener']) {
            $args['listener'] = [];
        }
        $args['isAdmin'] =      $isAdmin;
        $args['signalTypes'] =  $typeRepository->getSignalTypesSearched($args['type']);
        if ($isAdmin && $args['admin_mode'] !== '') {
            $args['show'] = 'list';
        }
        $paper =                isset($args['paper']) ? $paperRepository->getSpecifications($args['paper']) : false;
        $signals =              $signalRepository->getFilteredSignals($system, $args);
        $total =                $signalRepository->getFilteredSignalsCount($system, $args);
        $seeklistStats =        [];
        $seeklistColumns =      [];
        if ('seeklist' === $args['show']) {
            $seeklistStats =    SignalRepository::getSeeklistStats($signals);
            $seeklistColumns =  SignalRepository::getSeeklistColumns($signals, $paper);
        }
        $signalEntities = [];
        $signalTypes = [];
        foreach ($signals as $signal) {
            if ('seeklist' !== $args['show']) {
                $signal['first_heard'] =    $signal['first_heard'] ? new DateTime($signal['first_heard']) : null;
                $signal['last_heard'] =     $signal['last_heard'] ? new DateTime($signal['last_heard']) : null;
            }
            $s = new SignalEntity;
            $s->loadFromArray($signal);
            $signalEntities[] = $s;
            $signalTypes[] = $signal['type'];
        }
        $types = [];
        foreach ($signalTypes as $type) {
            $types[$type] = $typeRepository->getTypeForCode($type);
        }
        uasort($types, [$typeRepository, 'sortByOrder']);
        $expanded = [];
        foreach (SignalRepository::collapsable_sections as $section => $fields) {
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
            'args' =>               $args,
            'classic' =>            $this->systemRepository->getClassicUrl('signals', $args['show']),
            'columns' =>            $signalRepository->getColumns(),
            'expanded' =>           $expanded,
            'form' =>               $form->createView(),
            'isAdmin' =>            $isAdmin,
            'mode' =>               $this->translator->trans('Signals'),
            'paper' =>              $paper,
            'paperChoices' =>       $paperRepository->getAllChoices(),
            'personalised' =>       isset($args['personalise']) ? $listenerRepository->getDescription($args['personalise']) : false,
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : SignalRepository::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $total
            ],
            'seeklistColumns' =>    $seeklistColumns,
            'seeklistStats' =>      $seeklistStats,
            'signals' =>            $signalEntities,
            'stats' =>
                $signalRepository->getStats() +
                $listenerRepository->getStats($system, $args['rww_focus']),
            'system' =>             $system,
            'sortbyOptions' =>      $signalRepository->getColumns(),
            'tabs' => [
                [ 'list', 'Listing' ],
                [ 'seeklist', 'Seeklist'],
                [ 'map', 'Map' ],
            ],
            'types' =>              $types,
            'typeRepository' =>     $typeRepository
        ];
        if (isset($args['show']) && $args['show'] === 'csv') {
            $response = $this->render("signals/export/signals.csv.twig", $this->getMergedParameters($parameters));
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.csv");
            return $response;
        }
        return $this->render('signals/index.html.twig', $this->getMergedParameters($parameters));
    }

    private function setArgsFromRequest(&$args, $request)
    {
        $sets = [ 'type', 'listener' ];
        $pairs = [ 'khz', 'logged_date', 'logged_first', 'logged_last' ];
        foreach (array_keys($args) as $key) {
            if ($request->query->get($key)) {
                $args[$key] = $request->query->get($key);
            }
        }
        foreach ($sets as $set) {
            if ($request->query->get($set . 's')) {
                $args[$set] = [];
                $values = explode(',', $request->query->get($set . 's'));
                foreach ($values as $v) {
                    $args[$set][] = $v;
                }
            }
        }
        foreach ($pairs as $pair) {
            if ($request->query->get($pair)) {
                $values = explode(',', $request->query->get($pair));
                switch (count($values)) {
                    case 1:
                        $args[$pair . '_1'] = $values[0];
                        break;
                    case 2:
                        $args[$pair . '_1'] = min($values);
                        $args[$pair . '_2'] = max($values);
                        break;
                }
            }
        }
    }
}
