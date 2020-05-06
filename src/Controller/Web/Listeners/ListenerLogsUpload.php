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
            $this->parseFormat($format);
            $this->checkLogDateTokens();
            switch ($step) {
                case '1b':
                    if (!$this->errors && !$this->logHas['errors']) {
                        $this->saveFormat($data);
                    }
                    $step = '1';
                    break;
                case '2':
                    if ($this->errors || $this->logHas['errors']) {
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
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               $title,
            'errors' =>             $this->errors,
            'form' =>               $form->createView(),
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

    private function parseFormat($format) {
        $valid = array_merge(
            $this->logRepository::TOKENS['SINGLE'],
            $this->logRepository::TOKENS['MMDD'],
            $this->logRepository::TOKENS['YYYYMMDD']
        );
        $log_format_parse = $format . ' ';
        $start = 0;
        while (substr($log_format_parse, $start, 1) === ' ') {
            $start++;
        }
        while ($start < strlen($log_format_parse)) {
            $len =  strpos(substr($log_format_parse, $start), ' ');
            $key =  substr($log_format_parse, $start, $len);
            if ($len) {
                while (substr($log_format_parse, $start + $len, 1) === ' ') {
                    $len++;
                }
                if ($key === 'X' || !isset($this->tokens[$key])) {
                    $this->tokens[$key] = [ $start, $len + 1 ];
                    if (!in_array($key, $valid)) {
                        $this->errors[$key] = [
                            'class' =>  'unknown',
                            'msg' =>    'Token not recognised'
                        ];
                    }
                } else {
                    $this->errors[$key] = [
                        'class' =>  'duplicate',
                        'msg' =>    'Token occurs more than once'
                    ];
                }
            }
            $start += $len;
        }
    }

    protected function checkLogDateTokens()
    {
        $this->logHas = [
            'errors' => false,
            'YYYY' =>   false,
            'MM' =>     false,
            'DD' =>     false
        ];
        foreach ($this->logRepository::TOKENS['YYYYMMDD'] as $token) {
            if (isset($this->tokens[$token])) {
                $this->logHas['YYYY'] = true;
                $this->logHas['MM'] =   true;
                $this->logHas['DD'] =   true;
                return;
            }
        }
        foreach ($this->logRepository::TOKENS['MMDD'] as $token) {
            if (isset($this->tokens[$token])) {
                $this->logHas['MM'] =   true;
                $this->logHas['DD'] =   true;
                break;
            }
        }
        if (isset($this->tokens["YYYY"]) || isset($this->tokens["YY"])) {
            $this->logHas['YYYY'] = true;
        }
        if (isset($this->tokens["MMM"]) || isset($this->tokens["MM"]) || isset($this->tokens["M"])) {
            $this->logHas['MM'] =   true;
        }
        if (isset($this->tokens["DD"]) || isset($this->tokens["D"])) {
            $this->logHas['DD'] =   true;
        }
        if (!$this->logHas['YYYY'] || !$this->logHas['MM'] || $this->logHas['DD']) {
            $this->logHas['errors'] =   true;
        }
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
