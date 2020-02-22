<?php
namespace App\Controller\Web\Listeners\Export;

use App\Controller\Web\Listeners\Base;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Export
 */
class SignalsKml extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/export/signals.kml/{type}/{active}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"="", "type"="*", "active"="*"},
     *     name="listener_export_signals_kml"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param $type
     * @param $active
     * @param ListenerRepository $listenerRepository
     * @param TypeRepository $typeRepository
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $type,
        $active,
        ListenerRepository $listenerRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute(
                'listeners',
                [ '_locale' => $_locale, 'system' => $system ]
            );
        }
        $filter =                   [];
        if ($active !== '*') {
            $filter['active'] = $active;
        }
        if ($type !== '*') {
            $filter['type'] = explode(',', $type);
        }
        $parameters = [
            'colors' =>             $typeRepository->getMapIconColorForCodes(),
            'description' =>        "Generated by ".strToUpper($system)." on ".date('Y-m-d'),
            'filter' =>             $filter,
            '_locale' =>            $_locale,
            'signals' =>            $listenerRepository->getSignalsForListener($id, $filter),
            'title' =>              strToUpper($system).' stations received by '.$listener->getName(),
            'types' =>              $typeRepository->getAll()
        ];
        $parameters =   array_merge($parameters, $this->parameters);
        $filename =
             "listener_signals_"
            .$id
            .($type !== '*' ? "_type_".str_replace(',','_', $type) : '')
            .($active !== '*' ? "_".($active ? "active" : "inactive") : "")
            .".kml";
        $response =     $this->render('listener/export/signals.kml.twig', $parameters);
        $response->headers->set('Content-Disposition',"attachment;filename={$filename}");
        $response->headers->set('Content-Type', 'application/vnd.google-earth.kml+xml');
//        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
