<?php
namespace App\Controller;

use App\Entity\Signals;
use App\Repository\ModeRepository;
use App\Repository\SystemRepository;
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
    public function signalListController(
        $system,
        ModeRepository $modeRepository,
        SystemRepository $systemRepository
    ) {
        $parameters = [
            'mode' =>       'Signals',
            'modes' =>      $modeRepository->getAll(),
            'signal' => $this->getDoctrine()
                ->getRepository(Signals::class)
                ->find(1),
            'system' =>     $system,
            'systems' =>    $systemRepository->getAll()
        ];

        return $this->render('signals/index.html.twig', $parameters);
    }
}