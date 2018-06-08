<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Signals extends Controller {

    /**
     * @Route(
     *     "/{system}/signal_list",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals"
     * )
     */
    public function seeklistController($system)
    {
        return $this->render(
            'signals.html.twig',
            array(
                'system' => $system,
                'mode' => 'Signals'
            )
        );
    }
}