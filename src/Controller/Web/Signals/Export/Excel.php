<?php
namespace App\Controller\Web\Signals\Export;

use App\Controller\Web\Base as WebBase;
use App\Entity\Signal as SignalEntity;
use App\Repository\SignalRepository;
use App\Repository\TypeRepository;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Collection
 * @package App\Controller\Web\Signals\Export
 */
class Excel extends WebBase
{
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/export/excel",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_export_excel"
     * )
     * @param $_locale
     * @param $system
     * @param SignalRepository $signalRepository
     * @param TypeRepository $typeRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function controller(
        $_locale,
        $system,
        SignalRepository $signalRepository,
        TypeRepository $typeRepository
    ) {
        $args = [
            'limit' =>          -1,
            'order' =>          SignalRepository::defaultOrder,
            'signalTypes' =>    [0],
            'sort' =>           SignalRepository::defaultSorting,
            'system' =>         $system,
        ];
        $signals = $signalRepository->getFilteredSignals($system, $args);
        foreach ($signals as $signal) {
            $signal['first_heard'] =    $signal['first_heard'] ? new DateTime($signal['first_heard']) : null;
            $signal['last_heard'] =     $signal['last_heard'] ? new DateTime($signal['last_heard']) : null;
            $s = new SignalEntity;
            $s->loadFromArray($signal);
            $signalEntities[] = $s;
            $signalTypes[] = $signal['type'];
        }
        $types = [];
        foreach ($signalTypes as $type) {
            $types[$type] = $typeRepository->getTypeForCode($type);
        }
        $parameters = [
            '_locale' =>            $_locale,
            'args' =>               $args,
            'columns' =>            $signalRepository->getColumns(),
            'mode' =>               $this->translator->trans('Signals'),
            'signals' =>            $signalEntities,
            'system' =>             $system,
            'types' =>              $types,
            'typeRepository' =>     $typeRepository
        ];
        return $this->render('signals/index.html.twig', $this->getMergedParameters($parameters));
    }
}