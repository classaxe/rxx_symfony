<?php
namespace App\Controller\Web\Listeners;

use App\Controller\Web\Listeners\Base;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;

use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class ListenerStats extends Base
{

    /**
     * @Route(
     *     "/{system}/listeners/{id}/stats",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_stats"
     * )
     */
    public function statsController(
        $system,
        $id,
        ListenerRepository $listenerRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $parameters = [
            'id' =>                 $id,
            'mode' =>               $listener->getName().' &gt; Stats',
            'listener' =>           $listener,
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
            'typeRepository' =>     $typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/stats.html.twig', $parameters);
    }
}
