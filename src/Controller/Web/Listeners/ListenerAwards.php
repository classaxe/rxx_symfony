<?php
namespace App\Controller\Web\Listeners;

use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerAwards extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/awards",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_awards"
     * )
     */
    public function controller(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if ((int) $id) {
            if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               'Awards Available for '.$listener->getFormattedNameAndLocation(),
            'logs' =>               $listener->getCountLogs(),
            'signals' =>            $listener->getCountSignals(),
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/awards.html.twig', $parameters);
    }
}
