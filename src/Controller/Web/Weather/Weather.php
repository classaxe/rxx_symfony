<?php
namespace App\Controller\Web\Weather;

use App\Controller\Web\Base;

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
     * @return Response
     */
    public function controller($_locale, $system, Request $request)
    {
        $widgets =  $this->weatherRepository->getAll();
        $args =     $request->query->get('args');
        $gsq =  '';
        $lat =  '';
        $lon =  '';
        $zoom = 5;

        $spec = $this->getLightningCoords($system, $args);
        $lat =  $spec['lat'];
        $lon =  $spec['lon'];
        $zoom = $spec['zoom'];

        $spec = $this->getLightningCoords($system, $args);
        $gsq =  $args;
        $lat =  $spec['lat'];
        $lon =  $spec['lon'];
        $zoom = $spec['zoom'];

        $parameters = [
            '_locale' =>    $_locale,
            'args' =>       $args,
            'gsq' =>        $gsq,
            'lat' =>        $lat,
            'lon' =>        $lon,
            'mode' =>       'Weather',
            'system' =>     $system,
            'centers' =>    $this->weatherRepository->getCenters(),
            'widgets' =>    $widgets,
            'zoom' =>       $zoom
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
     * @return Response
     */
    public function widget($_locale, $system, $widget, $args, Request $request)
    {
        $parameters = $this->weatherRepository->get($widget);
        $parameters['_locale'] = $_locale;
        $parameters['system'] = $system;
        $parameters['key'] = $widget;
        $parameters['args'] = $args ? $args : $request->query->get('args');

        $gsq =  '';
        $lat =  '';
        $lon =  '';
        $zoom = 5;

        switch ($parameters['key']) {
            case 'lightning':
                $spec = $this->getLightningCoords($system, $parameters['args']);
                $gsq =  $parameters['args'];
                $lat =  $spec['lat'];
                $lon =  $spec['lon'];
                $zoom = $spec['zoom'];
            break;
        }

        $parameters['gsq'] = $gsq;
        $parameters['lat'] = $lat;
        $parameters['lon'] = $lon;
        $parameters['zoom'] = $zoom;
        $parameters['centers'] = $this->weatherRepository->getCenters();

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('weather/widget.html.twig', $parameters);
    }

    private function getLightningCoords($system, $args) {
        if ($args && $spec = $this->rxx::convertGsqToDegrees($args)) {
            $spec['zoom'] = 5;
        } else {
            switch ($system) {
                case 'rna':
                    $key = 'na';
                    break;
                case 'reu':
                    $key = 'eu';
                    break;
                case 'rww':
                    $key = 'as';
                    break;
            }
            $spec = $this->weatherRepository->getCenter($key);
        }
        $spec['lat'] = number_format($spec['lat'], 4);
        $spec['lon'] = number_format($spec['lon'], 4);
        return $spec;
    }
}
