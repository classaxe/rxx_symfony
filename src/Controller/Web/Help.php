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
     *     "/{_locale}/{system}/help",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="help"
     * )
     */
    public function helpController(
        $_locale,
        $system
    ) {
        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Help',
            'system' =>     $system,
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('help/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/help/admin",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="help/admin"
     * )
     */
    public function helpAdminController(
        $_locale,
        $system
    ) {
        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Admin Help',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('help/index.html.twig', $parameters);
    }
}
