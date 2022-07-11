<?php
namespace App\Controller\Web\Help;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Help extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/help",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="help"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function helpController($_locale, $system) {
        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Help',
            'system' =>     $system,
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('help/index.html.twig', $parameters);
    }
}
