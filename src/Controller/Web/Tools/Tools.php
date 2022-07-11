<?php
namespace App\Controller\Web\Tools;

use App\Controller\Web\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Tools
 * @package App\Controller\Web\Tools
 */
class Tools extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/tools",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="tools"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @return Response
     */
    public function index($_locale, $system, Request $request)
    {
        $widgets =   $this->toolRepository->getAll();

        $parameters = [
            '_locale' =>    $_locale,
            'args' =>       $request->query->get('args'),
            'mode' =>       'Tools',
            'system' =>     $system,
            'widgets' =>    $widgets
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('tools/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/tools/{widget}/{args}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"args"=""},
     *     name="tools_widget"
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
        $parameters = $this->toolRepository->get($widget);
        $parameters['_locale'] = $_locale;
        $parameters['system'] = $system;
        $parameters['key'] = $widget;
        $parameters['args'] = $args ? $args : $request->query->get('args');

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('tools/widget.html.twig', $parameters);
    }

}
