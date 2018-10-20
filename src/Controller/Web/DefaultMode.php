<?php
namespace App\Controller\Web;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultMode extends Controller
{
    /**
     * @Route(
     *     "/{system}/",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="system"
     * )
     * @param $system
     */
    public function defaultModeController($system)
    {
        $parameters =['system' => $system];

        return $this->redirectToRoute("signals", $parameters);
    }
}