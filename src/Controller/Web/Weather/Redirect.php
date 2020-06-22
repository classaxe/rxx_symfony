<?php
namespace App\Controller\Web\Weather;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Weather
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/weather/{widget}",
     *     defaults={"widget"=""}
     * )
     * @param $widget
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_1($widget, GeoService $GeoService)
    {
        $parameters = [
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $GeoService->getDefaultSystem(),
            'widget' =>     $widget
        ];
        if ($widget) {
            return $this->redirectToRoute('weather_widget', $parameters);
        }
        return $this->redirectToRoute('weather', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/weather/{widget}",
     *     defaults={"widget"=""},
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     * )
     * @param $_locale
     * @param $widget
     * @param GeoService $GeoService
     * @return RedirectResponse
     */
    public function redirect_2($_locale, $widget, GeoService $GeoService)
    {
        $parameters = [
            '_locale' =>    $_locale,
            'system' =>     $GeoService->getDefaultSystem(),
            'widget' =>     $widget
        ];
        if ($widget) {
            return $this->redirectToRoute('weather_widget', $parameters);
        }
        return $this->redirectToRoute('weather', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/weather/{widget}",
     *     defaults={"widget"=""},
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $system
     * @param $widget
     * @return RedirectResponse
     */
    public function redirect_3($system, $widget)
    {
        $parameters =[
            '_locale' =>    $this->get('session')->get('_locale'),
            'system' =>     $system,
            'widget' =>     $widget
        ];
        if ($widget) {
            return $this->redirectToRoute('weather_widget', $parameters);
        }
        return $this->redirectToRoute('weather', $parameters);
    }
}
