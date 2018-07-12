<?php
namespace App\Controller;

use App\Form\Logon as LogonForm;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class ListenerList
 * @package App\Controller
 */
class Help extends BaseController
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
    public function logonController(
        $system
    ) {
        $parameters = [
            'mode' =>       'Help',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('help/index.html.twig', $parameters);
    }
}
