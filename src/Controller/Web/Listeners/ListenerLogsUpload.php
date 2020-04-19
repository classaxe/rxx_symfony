<?php
namespace App\Controller\Web\Listeners;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerLogsUpload extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logsupload",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logsupload"
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
            if (!$listener = $this->getValidReportingListener($id)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }

        $isAdmin = $this->parameters['isAdmin'];

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               'Upload Loggings for '.$listener->getFormattedNameAndLocation(),
            'logs' =>               $listener->getCountLogs(),
            'signals' =>            $listener->getCountSignals(),
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/upload.html.twig', $parameters);
    }
}
