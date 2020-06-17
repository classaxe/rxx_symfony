<?php
namespace App\Controller\Web\Weather;

use App\Controller\Web\Base;
use App\Repository\WeatherRepository;
use App\Utils\Rxx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Weather extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/weather",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="weather"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param WeatherRepository $weatherRepository
     * @return Response
     */
    public function controller($_locale, $system, Request $request, WeatherRepository $weatherRepository)
    {
        $widgets =   $weatherRepository->getAll();

        $parameters = [
            '_locale' =>    $_locale,
            'args' =>       $request->query->get('args'),
            'mode' =>       'Weather',
            'system' =>     $system,
            'classic' =>    $this->systemRepository->getClassicUrl('weather'),
            'widgets' =>    $widgets
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('weather/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/weather/{widget}/{args}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"args"=""},
     *     name="weather_widget"
     * )
     * @param $_locale
     * @param $system
     * @param $widget
     * @param $args
     * @param Request $request
     * @param WeatherRepository $weatherRepository
     * @return Response
     */
    public function widget($_locale, $system, $widget, $args, Request $request, WeatherRepository $weatherRepository)
    {
        $parameters = $weatherRepository->get($widget);
        $parameters['_locale'] = $_locale;
        $parameters['system'] = $system;
        $parameters['key'] = $widget;
        $parameters['args'] = $args ? $args : $request->query->get('args');
        switch ($parameters['key']) {
            case 'lightning':
                if ($parameters['args'] && $a = Rxx::convertGsqToDegrees($parameters['args'])) {
                    $lat =  $a["lat"];
                    $lon =  $a["lon"];
                    $gsq =  $a["GSQ"];
                } else {
                    $gsq =  '';
                    $lat =  '';
                    $lon =  '';
                }
                $parameters['gsq'] = $gsq;
                $parameters['lat'] = $lat;
                $parameters['lon'] = $lon;
                $parameters['zoom'] = 3;
                break;
        }

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('weather/widget.html.twig', $parameters);
    }

}
