<?php
namespace App\Controller\Web\Tools;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Tools
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/tools"
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

        return $this->redirectToRoute('tools', $parameters);
    }

    /**
     * @Route(
     *     "/tools/{widget}/{args}",
     *     defaults={"args"=""}
     * )
     * @param $widget
     * @param $args
     * @param Request $request
     * @param GeoService $GeoService
     * @return Response
     */
    public function redirect_1b($widget, $args, Request $request, GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $GeoService->getDefaultSystem(),
            'widget' => $widget,
            'args' =>   $args ? $args : $request->query->get('args')
        ];

        return $this->redirectToRoute('tools_widget', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/tools",
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

        return $this->redirectToRoute('tools', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/tools/{widget}/{args}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     *     defaults={"args"=""}
     * )
     * @param $_locale
     * @param $widget
     * @param $args
     * @param Request $request
     * @param GeoService $GeoService
     * @return Response
     */
    public function redirect_2b($_locale, $widget, $args, Request $request, GeoService $GeoService)
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $GeoService->getDefaultSystem(),
            'widget' => $widget,
            'args' =>   $args ? $args : $request->query->get('args')
        ];

        return $this->redirectToRoute('tools_widget', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/tools",
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

        return $this->redirectToRoute('tools', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/tools/{widget}/{args}",
     *     defaults={"args"=""}
     * )
     * @param $system
     * @param $widget
     * @param $args
     * @param Request $request
     * @return Response
     */
    public function redirect_3b($system, $widget, $args, Request $request)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $system,
            'widget' => $widget,
            'args' =>   $args ? $args : $request->query->get('args')
        ];

        return $this->redirectToRoute('tools_widget', $parameters);
    }

}
