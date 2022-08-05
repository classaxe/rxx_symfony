<?php
namespace App\Controller\Web\Logsessions;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Logsessions
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/logsessions",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     }
     * )
     * @param GeoService $GeoService
     * @return RedirectResponse|Response
     */
    public function redirect_1(GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('logsessions', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/logsessions",
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

        return $this->redirectToRoute('logsessions', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/logsessions",
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

        return $this->redirectToRoute('logsessions', $parameters);
    }

}
