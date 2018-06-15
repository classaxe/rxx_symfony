<?php
namespace App\Controller;

use App\Entity\Signals;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SignalList extends Controller {

    /**
     * @Route(
     *     "/{system}/signal_list",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_list"
     * )
     */
    public function seeklistController($system)
    {
        $parameters = [
            'system' => $system,
            'mode' => 'Signals',
            'signal' => $this->getDoctrine()
                ->getRepository(Signals::class)
                ->find(1)
        ];

        return $this->render('signals/index.html.twig', $parameters);
    }
}