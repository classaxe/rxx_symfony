<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\LogUpload as LogUploadForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     *     "/{_locale}/{system}/listeners/{id}/upload",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logsupload"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     *
     * @param LogUploadForm $logUploadForm
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        LogUploadForm $logUploadForm
    ) {
        $isAdmin = $this->parameters['isAdmin'];

        if (!$isAdmin || !$listener = $this->getValidListener($id)) {
            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }
        $options = [
            'id' =>         $listener->getId(),
            'format' =>     $listener->getLogFormat()
        ];

        $form = $logUploadForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
        }

        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               'Upload Loggings for '.$listener->getFormattedNameAndLocation(),
            'form' =>               $form->createView(),
            'logs' =>               $listener->getCountLogs(),
            'signals' =>            $listener->getCountSignals(),
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/upload.html.twig', $parameters);
    }
}
