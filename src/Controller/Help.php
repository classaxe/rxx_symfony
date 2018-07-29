<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ListenerList
 * @package App\Controller
 */
class Help extends Base
{

    private $username = '';
    private $password = '';

    /**
     * @Route(
     *     "/{system}/help",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="help"
     * )
     */
    public function helpController(
        $system
    ) {
        $parameters = [
            'mode' =>       'Help',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('help/index.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{system}/help/admin",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="help/admin"
     * )
     */
    public function helpAdminController(
        $system
    ) {
        $parameters = [
            'mode' =>       'Admin Help',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('help/index.html.twig', $parameters);
    }
}
