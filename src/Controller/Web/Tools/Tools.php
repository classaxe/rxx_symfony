<?php
namespace App\Controller\Web\Tools;

use App\Controller\Web\Base;
use App\Repository\ToolRepository;
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
     * @param ToolRepository $toolRepository
     * @return Response
     */
    public function index($_locale, $system, ToolRepository $toolRepository)
    {
        $tools =   $toolRepository->getAll();

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Tools',
            'system' =>     $system,
            'classic' =>    $this->systemRepository->getClassicUrl('tools'),
            'title' =>      'Tools',
            'tools' =>      $tools['tools']
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('tools/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/tools/{tool}/{args}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"args"=""},
     *     name="tool"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param $tool
     * @param $args
     * @param ToolRepository $toolRepository
     * @return Response
     */
    public function tool($_locale, $system, Request $request, $tool, $args, ToolRepository $toolRepository)
    {
        $parameters = $toolRepository->get($tool);
        $parameters['_locale'] = $_locale;
        $parameters['system'] = $system;
        $parameters['key'] = $tool;
        $parameters['args'] = $args ? $args : $request->query->get('args');

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('tools/tool.html.twig', $parameters);
    }

}
