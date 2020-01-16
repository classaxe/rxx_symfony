<?php
namespace App\Controller\Web;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultSystem
 * @package App\Controller\Web
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/",
     *     name="home"
     * )
     * @return RedirectResponse
     */
    public function localeController()
    {
        $parameters = [ '_locale' => $this->get('session')->get('_locale') ];

        return $this->redirectToRoute('system', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     *     name="system"
     * )
     * @param $_locale
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function defaultSystemController($_locale, GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('mode', $parameters);
    }

    /**
     * @Route(
     *     "/{system}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="_mode"
     * )
     * @param $system
     * @return RedirectResponse
     */
    public function defaultLocaleAndModeController($system)
    {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system
        ];

        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="mode"
     * )
     * @param $_locale
     * @param $system
     * @return RedirectResponse
     */
    public function defaultModeController(
        $_locale,
        $system
    ) {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system
        ];

        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/signals",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="_signals"
     * )
     * @param $system
     * @return RedirectResponse
     */
    public function signalsRedirector($system) {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system
        ];

        return $this->redirectToRoute('signals', $parameters, 301);
    }

    /**
     * @Route(
     *     "/{system}/listeners/{id}/signalmap",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_export_signalmap_redirect"
     * )
     * @param $system
     * @param $id
     * @return RedirectResponse
     */
    public function listenerSignalsMapRedirector ($system, $id)
    {
        return $this->redirectToRoute(
            'listener_export_signalmap',
            ['_locale' => 'en', 'system' => $system, 'id' => $id]
        );
    }




}
