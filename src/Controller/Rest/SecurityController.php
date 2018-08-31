<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-08-23
 * Time: 20:55
 */

namespace App\Controller\Rest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/login", name="login")
     */
    public function login(Request $request)
    {
    }
}
