<?php
namespace App\Controller\Web\Cle;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Cle
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/cle"
     * )
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_1(GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('cle', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/cle",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     * )
     * @param $_locale
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_2($_locale, GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('cle', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/cle",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_3($system)
    {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system
        ];

        return $this->redirectToRoute('cle', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/cle/signals/{id}/map",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse
     */
    public function redirect_4($_locale, $system, $id)
    {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system,
            'id' =>         $id
        ];

        return $this->redirectToRoute('signal_map', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/cle/signals/{id}/map/eu",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse
     */
    public function redirect_5($_locale, $system, $id)
    {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system,
            'id' =>         $id
        ];

        return $this->redirectToRoute('signal_rx_map_eu', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/cle/signals/{id}/map/na",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse
     */
    public function redirect_6($_locale, $system, $id)
    {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system,
            'id' =>         $id
        ];

        return $this->redirectToRoute('signal_rx_map_na', $parameters);
    }

}
