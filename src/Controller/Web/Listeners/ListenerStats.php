<?php
namespace App\Controller\Web\Listeners;

use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class ListenerStats extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/stats",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_stats"
     * )
     */
    public function controller(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               $listener->getName().' &gt; Stats',
            'listener' =>           $listener,
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener, $isAdmin),
            'typeRepository' =>     $typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('listener/stats.html.twig', $parameters);
    }
}
