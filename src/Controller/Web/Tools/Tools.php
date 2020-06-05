<?php
namespace App\Controller\Web\Tools;

use App\Controller\Web\Base;
use App\Repository\ToolRepository;
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
     *     "/{_locale}/{system}/tools/{tool}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="tool"
     * )
     * @param $_locale
     * @param $system
     * @param $tool
     * @param ToolRepository $toolRepository
     * @return Response
     */
    public function coordinates($_locale, $system, $tool, ToolRepository $toolRepository)
    {
        $parameters = $toolRepository->get($tool);
        $parameters['_locale'] = $_locale;
        $parameters['system'] = $system;
        $parameters['key'] = $tool;

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('tools/tool.html.twig', $parameters);
    }

}
