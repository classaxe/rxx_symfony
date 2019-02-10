<?php
namespace App\Controller\Web;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultSystem
 * @package App\Controller\Web
 */
class Locale extends AbstractController
{

    /**
     * @Route(
     *     "/",
     *     name="home"
     * )
     */
    public function localeController()
    {

        $parameters = [
            '_locale' => $this->get('session')->get('_locale')
        ];

        return $this->redirectToRoute('system', $parameters);
    }
}
