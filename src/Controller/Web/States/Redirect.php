<?php
namespace App\Controller\Web\States;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\States
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/states"
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

        return $this->redirectToRoute('states', $parameters);
    }

    /**
     * @Route(
     *     "/states/{filter}"
     * )
     * @param $filter
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_2(GeoService $GeoService, $filter)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $GeoService->getDefaultSystem(),
            'filter' => $filter
        ];

        return $this->redirectToRoute('states', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/states",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     * )
     * @param $_locale
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_3($_locale, GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('states', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/states/{filter}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     * )
     * @param $_locale
     * @param GeoService $GeoService
     * @param $filter
     * @return RedirectResponse
     */
    public function redirect_4($_locale, GeoService $GeoService, $filter)
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $GeoService->getDefaultSystem(),
            'filter' => $filter
        ];

        return $this->redirectToRoute('states', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/states",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_5($system)
    {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system
        ];

        return $this->redirectToRoute('states', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/states/{filter}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     }
     * )
     * @param $system
     * @param $filter
     * @return RedirectResponse
     */
    public function redirect_6($system, $filter)
    {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system,
            'filter' => $filter
        ];

        return $this->redirectToRoute('states', $parameters);
    }
}
