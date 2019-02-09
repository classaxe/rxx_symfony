<?php
namespace App\Controller\Web;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Help extends Base
{

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $password = '';

    /**
     * @Route(
     *     "/{locale}/{system}/help",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="help"
     * )
     */
    public function helpController(
        $locale,
        $system
    ) {
        $parameters = [
            'locale' =>     $locale,
            'mode' =>       'Help',
            'system' =>     $system,
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('help/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{locale}/{system}/help/admin",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="help/admin"
     * )
     */
    public function helpAdminController(
        $locale,
        $system
    ) {
        $parameters = [
            'locale' =>     $locale,
            'mode' =>       'Admin Help',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('help/index.html.twig', $parameters);
    }
}
