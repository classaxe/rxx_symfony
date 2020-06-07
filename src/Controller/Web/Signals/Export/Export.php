<?php
namespace App\Controller\Web\Signals\Export;

use App\Controller\Web\Base as Base;
use App\Entity\Signal as SignalEntity;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Collection
 * @package App\Controller\Web\Signals\Export
 */
class Export extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/export/csv",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_export_csv"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     * @throws Exception
     */
    public function csv(
        $_locale,
        $system
    ) {
        return $this->export($_locale, $system, 'csv');
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/export/txt",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_export_txt"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     * @throws Exception
     */
    public function txt(
        $_locale,
        $system
    ) {
        return $this->export($_locale, $system, 'txt');
    }

    /**
     * @param $_locale
     * @param $system
     * @param $mode
     * @return Response
     * @throws Exception
     */
    private function export(
        $_locale,
        $system,
        $mode
    ) {
        $args = [
            'limit' =>          -1,
            'order' =>          $this->signalRepository::defaultOrder,
            'signalTypes' =>    [0],
            'sort' =>           $this->signalRepository::defaultSorting,
            'system' =>         $system,
        ];
        $signals = $this->signalRepository->getFilteredSignals($system, $args);
        $signalEntities = [];
        $signalTypes = [];
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
            $types[$type] = $this->typeRepository->getTypeForCode($type);
        }
        $parameters = [
            '_locale' =>            $_locale,
            'args' =>               $args,
            'columns' =>            $this->signalRepository->getColumns('signals'),
            'mode' =>               $this->i18n('Signals'),
            'signals' =>            $signalEntities,
            'system' =>             $system,
            'types' =>              $types,
            'typeRepository' =>     $this->typeRepository
        ];
        switch ($mode) {
            case 'csv':
                $response = $this->render("signals/export/signals.csv.twig", $parameters);
                break;
            case 'txt':
                $response = $this->render("signals/export/signals.txt.twig", $parameters);
                break;
        }
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition',"attachment;filename={$system}_signals.{$mode}");
        return $response;
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/export/js/dgps",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_export_js_dgps"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     * @throws Exception
     */
    public function jsDgps() {
        $signals = $this->signalRepository->getDgpsForLookup();
        $response = new Response($signals , 200);
        $response->headers->set('Content-Type', 'application/javascript');
        return $response;
    }

}