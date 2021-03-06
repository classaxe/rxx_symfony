<?php
namespace App\Controller\Web\Admin;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Admin
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/{_locale}/{system}/admin",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     }
     * )
     * @param $_locale
     * @param $system
     * @return RedirectResponse|Response
     */
    public function redirect_1($_locale, $system)
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $system
        ];

        return $this->redirectToRoute('logon', $parameters);
    }

    /**
     * @Route(
     *     "/admin"
     * )
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_2(GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $GeoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('logon', $parameters);
    }

}
