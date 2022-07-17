<?php
namespace App\Controller\Web\Listeners;

use App\Entity\Listener as ListenerEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listeners
 */
class ListenerMap extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/map",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_map"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id
    ) {
        if ((int) $id) {
            if (!$listener = $this->getValidListener($id)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        } else {
            $listener = new ListenerEntity();
        }

        $isAdmin = $this->parameters['isAdmin'];
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'lat' =>                $listener->getLat(),
            'lon' =>                $listener->getLon(),
            'mode' =>               'Map | ' . $listener->getFormattedNameAndLocation(),
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/map.html.twig', $parameters);
    }
}
