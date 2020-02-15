<?php
namespace App\Controller\Web\Maps;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Maps
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/maps"
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

        return $this->redirectToRoute('maps', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/maps",
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

        return $this->redirectToRoute('listeners', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/maps",
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

        return $this->redirectToRoute('listeners', $parameters);
    }
}
