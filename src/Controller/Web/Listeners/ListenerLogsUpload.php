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
    private $listener;
    private $logHas;
    private $tokens;
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
            switch ($step) {
                case '1b':
                    $this->saveFormat($data);
                    $step = '1';
                    break;
                case '2':
                    $this->parseLog($data);
                    break;
            }
        }
        $this->parseFormat($format);
        $this->checkLogDateTokens();
        $title = sprintf(
            $this->i18n('Upload Loggings for %s | Step %d'),
            $this->listener->getFormattedNameAndLocation(),
            $step
        );
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               $title,
            'form' =>               $form->createView(),
            'formatOld' =>          $this->listener->getLogFormat(),
            'has' =>                $this->logHas,
            'logs' =>               $this->listener->getCountLogs(),
            'signals' =>            $this->listener->getCountSignals(),
            'step' =>               $step,
            'system' =>             $this->system,
            'tabs' =>               $this->listenerRepository->getTabs($this->listener, $isAdmin)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/upload.html.twig', $parameters);
    }

    private function parseFormat($format) {
        $valid = array_merge(
            $this->logRepository::TOKENS['SINGLE'],
            $this->logRepository::TOKENS['MMDD'],
            $this->logRepository::TOKENS['YYYYMMDD']
        );
        $this->tokens = [];
        $tokens =       [];
        $flags =        [];
        $start = 0;
        $log_format_parse =     $format . ' ';
        $log_format_errors =    '';
        while (substr($log_format_parse, $start, 1) === " ") {
            $start++;
        }
        while ($start<strlen($log_format_parse)) {
            $len =        strpos(substr($log_format_parse, $start), " ");
            $param_name =    substr($log_format_parse, $start, $len);
            if ($len) {
                while (substr($log_format_parse, $start+$len, 1)==" ") {
                    $len++;
                }
                if ($param_name=="X" || !isset($this->tokens[$param_name])) {
                    $this->tokens[$param_name] = array($start,$len+1);
                    if (!in_array($param_name, $valid)) {
                        $tokens[] = $param_name;
                        $flags[] =
                            "<span style='color:#ff0000;font-weight:bold;cursor:pointer'"
                            ." title='Token not recognised'>"
                            .$param_name
                            ."</span>";
                        $log_format_errors.=
                            "<tr class='rownormal'>\n"
                            ."  <th align='left'>".$param_name."</th>\n"
                            ."  <td><span style='color:#ff0000;'>Token not recognised</span></td>\n"
                            ."</tr>\n";

                    }
                } else {
                    $tokens[] = $param_name;
                    $flags[] =
                        "<span style='color:#ff00ff;font-weight:bold;cursor:pointer'"
                        ." title='Token occurs more than once'>".$param_name."</span>";
                    $log_format_errors.=
                        "<tr class='rownormal'>\n"
                        ."  <th align='left'>".$param_name."</th>\n"
                        ."  <td><span style='color:#ff00ff;'>Token occurs more than once</span></td>\n"
                        ."</tr>\n";
                }
            }
            $start = $start+$len;
        }
    }

    protected function checkLogDateTokens()
    {
        $this->logHas = [
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
