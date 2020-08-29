<?php
namespace App\Controller\Web\Signals\Export;

use App\Controller\Web\Base as Base;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function csv(
        $_locale,
        $system,
        Request $request
    ) {
        return $this->export($_locale, $system, 'csv', $request);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/export/kml",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_export_kml"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function kml(
        $_locale,
        $system,
        Request $request
    ) {
        return $this->export($_locale, $system, 'kml', $request);
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
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function txt(
        $_locale,
        $system,
        Request $request
) {
        return $this->export($_locale, $system, 'txt', $request);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/export/xls",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_export_xls"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function xls(
        $_locale,
        $system,
        Request $request
    ) {
        return $this->export($_locale, $system, 'xls', $request);
    }

    /**
     * @param $_locale
     * @param $system
     * @param $mode
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    private function export(
        $_locale,
        $system,
        $mode,
        Request $request
    ) {
        $args = [
            'limit' =>          -1,
            'order' =>          $this->signalRepository::defaultOrder,
            'signalTypes' =>    [0],
            'sort' =>           $this->signalRepository::defaultSorting,
            'system' =>         $system,
        ];
        $this->setTypeFromRequest($args, $request);
        $args['signalTypes'] =  $this->typeRepository->getSignalTypesSearched($args['type']);
        $signals = $this->signalRepository->getFilteredSignals($system, $args);
        $signalEntities = [];
        $signalTypes = [];
        foreach ($signals as $signal) {
            $signal['first_heard'] =    $signal['first_heard'] ? new DateTime($signal['first_heard']) : null;
            $signal['last_heard'] =     $signal['last_heard'] ? new DateTime($signal['last_heard']) : null;
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
            'signals' =>            $signals,
            'system' =>             $system,
            'title' =>              $this->i18n('Signals'),
            'types' =>              $types,
            'typeRepository' =>     $this->typeRepository
        ];
        switch ($mode) {
            case 'csv':
                $type = 'text/plain';
                $name = "{$system}_signals.csv";
                $response = $this->render("signals/export/signals.csv.twig", $parameters);
                break;
            case 'kml':
                $type = 'application/vnd.google-earth.kml+xml';
                $name = "{$system}_signals.kml";
                $response = $this->render("signals/export/signals.kml.twig", $parameters);
                break;
            case 'txt':
                $type = 'text/plain';
                $name = "{$system}_signals.txt";
                $response = $this->render("signals/export/signals.txt.twig", $parameters);
                break;
            case 'xls':
                $type = 'application/vnd.ms-excel';
                $name = "export_" . strtoupper($system) . ".xls";
                $response = $this->render("signals/export/signals.xls.twig", $parameters);
                break;
        }
        $response->headers->set('Content-Type', $type);
        $response->headers->set('Content-Disposition',"attachment;filename={$name}");
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