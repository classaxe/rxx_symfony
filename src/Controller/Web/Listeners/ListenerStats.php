<?php
namespace App\Controller\Web\Listeners;

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
        $id
    ) {
        if (!$listener = $this->getValidListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               'Statistics | ' . $listener->getFormattedNameAndLocation(),
            'listener' =>           $listener,
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin),
            'typeRepository' =>     $this->typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('listener/stats.html.twig', $parameters);
    }
}
