<?php
namespace App\Controller\Web\Tools;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

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
     * @return Response
     */
    public function controller($_locale, $system)
    {
        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Donate',
            'system' =>     $system,
            'classic' =>    $this->systemRepository->getClassicUrl('tools'),
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('tools/index.html.twig', $parameters);
    }

}
