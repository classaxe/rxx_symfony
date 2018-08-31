<?php
namespace App\Controller\Web;

use App\Entity\Signal as SignalsEntity;
use App\Repository\ModeRepository;
use App\Repository\SystemRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Signals extends Base
{

    /**
     * @Route(
     *     "/{system}/signals",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="signals"
     * )
     */
    public function signalsController(
        $system
    ) {
        $parameters = [
            'mode' =>       'Signals',
            'signal' => $this->getDoctrine()
                ->getRepository(SignalsEntity::class)
                ->find(1),
            'system' =>     $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signals/index.html.twig', $parameters);
    }
}
