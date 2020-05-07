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
    private $errors = [];
    private $listener;
    private $logHas;
    private $tokens = [];
    private $system;

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
        $this->system = $system;
        if (!$isAdmin || !$this->listener = $this->getValidListener($id)) {
            return $this->redirectToRoute('listener', ['system' => $this->system, 'id' => $id]);
        }
        $step = $request->get('step', '1');
        $format = $this->listener->getLogFormat();
        $options = [
            'id' =>         $this->listener->getId(),
            'step' =>       $step,
            'format' =>     $format
        ];

        $form = $logUploadForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $step = $data['step'];
            $format = $data['format'];
            list($this->tokens, $this->errors) = $this->logRepository->parseFormat($format);
            $this->logHas = $this->logRepository->checkLogDateTokens($this->tokens);
            switch ($step) {
                case '1b':
                    if (!$this->errors && !$this->logHas['partial']) {
                        $this->saveFormat($data);
                    }
                    $step = '1';
                    break;
                case '2':
                    if ($this->errors || $this->logHas['partial']) {
                        $step = '1';
                    } else {
                        $this->parseLog($data);
                    }
                    break;
            }
        }
        $title = sprintf(
            $this->i18n('Upload Loggings for %s | Step %d'),
            $this->listener->getFormattedNameAndLocation(),
            $step
        );
        $form_logs_height =
            420 - ($this->errors ? 90 + (23 * count($this->errors)) : 0) - ($this->logHas['partial'] ? 28 : 0);
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               $title,
            'errors' =>             $this->errors,
            'form' =>               $form->createView(),
            'form_logs_height' =>   $form_logs_height,
            'format' =>             $format,
            'formatOld' =>          $this->listener->getLogFormat(),
            'has' =>                $this->logHas,
            'logs' =>               $this->listener->getCountLogs(),
            'signals' =>            $this->listener->getCountSignals(),
            'step' =>               $step,
            'system' =>             $this->system,
            'tabs' =>               $this->listenerRepository->getTabs($this->listener, $isAdmin),
            'tokens' =>             $this->tokens
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/logs_upload/index.html.twig', $parameters);
    }

    private function parseLog($data) {
        print "<pre>" . print_r($data, true) . "</pre>";
    }

    private function saveFormat($data) {
        $this->listener->setLogFormat($data['format']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->listener);
        $em->flush();
        $parameters  = [
            'system' => $this->system,
            'id' =>     $this->listener->getId()
        ];
        $this->redirectToRoute('listener_logsupload', $parameters);
    }
}
