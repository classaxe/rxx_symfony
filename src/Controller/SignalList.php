<?php
namespace App\Controller;

use App\Entity\Signals;
use App\Repository\ModeRepository;
use App\Repository\SystemRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SignalList extends BaseController {

    /**
     * @Route(
     *     "/{system}/signal_list",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_list"
     * )
     */
    public function signalListController(
        $system
    ) {
        $parameters = [
            'mode' =>       'Signals',
            'signal' => $this->getDoctrine()
                ->getRepository(Signals::class)
                ->find(1),
            'system' =>     $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signals/index.html.twig', $parameters);
    }
}