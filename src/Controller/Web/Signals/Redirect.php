<?php
namespace App\Controller\Web\Signals;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Signals
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/"
     * )
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_1(GeoService $GeoService): RedirectResponse
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/signals"
     * )
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_2(GeoService $GeoService): RedirectResponse
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     * )
     * @param $_locale
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_3($_locale, GeoService $GeoService): RedirectResponse
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/signals",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     * )
     * @param $_locale
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_4($_locale, GeoService $GeoService): RedirectResponse
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{system}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_5($system): RedirectResponse
    {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
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
     * )
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_6($system): RedirectResponse
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
     *     name = "locale_system"
     * )
     * @param $_locale
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_7($_locale, $system): RedirectResponse
    {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system
        ];

        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/seeklist",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_seeklist"
     * )
     * @param $_locale
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_8($_locale, $system): RedirectResponse
    {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system,
            'show' =>       'seeklist'
        ];
        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/map",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_map"
     * )
     * @param $_locale
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_9($_locale, $system): RedirectResponse
    {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system,
            'show' =>       'map'
        ];
        return $this->redirectToRoute('signals', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/signals",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals_map"
     * )
     * @param $_locale
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_10($_locale, $system): RedirectResponse
    {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system
        ];
        return $this->redirectToRoute('signals', $parameters);
    }

}
