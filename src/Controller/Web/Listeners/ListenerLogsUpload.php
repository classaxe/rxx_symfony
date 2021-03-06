<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\LogUpload as LogUploadForm;
use DateTime;
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
    private $entries = [];
    private $errors = [];
    private $listener;
    private $logs;
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
        $userName = $this->parameters['user_name'];
        $this->system = $system;
        if (!$isAdmin || !$this->listener = $this->getValidListener($id)) {
            return $this->redirectToRoute('listener', ['system' => $this->system, 'id' => $id]);
        }
        $heardIn = $this->listener->getSp() ? $this->listener->getSp() : $this->listener->getItu();
        $region = $this->listener->getRegion();
        $step = $request->get('step', '1');
        $selected = 'UNSET';
        $format = $this->listener->getLogFormat();
        $options = [
            'id' =>         $this->listener->getId(),
            'step' =>       $step,
            'selected' =>   $selected,
            'format' =>     $format
        ];
        $stats = [
            'duplicates' => 0,
            'first_for_listener' => 0,
            'first_for_place' => 0,
            'latest_for_signal' => 0,
            'logs' => 0,
            'logs_dgps' => 0,
            'logs_dsc' => 0,
            'logs_hambcn' => 0,
            'logs_navtex' => 0,
            'logs_ndb' => 0,
            'logs_other' => 0,
            'logs_time' => 0,
            'repeat_for_listener' => 0
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
            $this->logs =   $data['logs'];
            $selected =     $data['selected'];
            $this->errors = [];
            $this->logRepository->parseFormat($format, $this->tokens, $this->errors, $this->logHas);

            $YYYY = $this->logHas['YYYY'] ? '' : $data['YYYY'];
            $MM =   $this->logHas['MM']   ? '' : $data['MM'];
            $DD =   $this->logHas['DD']   ? '' : $data['DD'];

            switch ($step) {
                case '1b':
                    if (!$this->errors && !$this->logHas['partial']) {
                        $this->saveFormat($data['format']);
                    }
                    $step = '1';
                    break;
                case '2':
                    if ($this->errors || (!$this->logHas['YYYY'] && !$YYYY) || (!$this->logHas['MM'] && !$MM) || (!$this->logHas['DD'] && !$DD)) {
                        $step = '1';
                        break;
                    }
                    $this->entries = $this->logRepository->parseLog(
                        $this->listener,
                        $this->logs,
                        $this->tokens,
                        $YYYY,
                        $MM,
                        $DD,
                        $this->signalRepository,
                        false
                    );
                    $step = '2';
                    break;
                case '3':
                    if ($this->errors || (!$this->logHas['YYYY'] && !$YYYY) || (!$this->logHas['MM'] && !$MM) || (!$this->logHas['DD'] && !$DD)) {
                        $step = '1';
                        break;
                    }
                    $this->entries = $this->logRepository->parseLog(
                        $this->listener,
                        $this->logs,
                        $this->tokens,
                        $YYYY,
                        $MM,
                        $DD,
                        $this->signalRepository,
                        $selected
                    );
                    $user = $this->userRepository->find($this->session->get('user_id'));

                    $logSessionID = $this->logsessionRepository->addLogSession(
                        new DateTime(),
                        $user->getId(),
                        $this->listener->getId()
                    );

                    $firstLog = null;
                    $lastLog = null;
                    foreach($this->entries as $e) {
                        if ($this->logRepository->checkIfDuplicate($e['signalID'], $id, $e['YYYYMMDD'], $e['time'])) {
                            $stats['duplicates']++;
                            continue;
                        }
                        $stats['logs']++;
                        $type = $this->typeRepository->getTypeForCode($e['type']);
                        $stats['logs_' . strtolower($type['class'])]++;
                        $datestamp = $e['YYYYMMDD'] . ' ' . substr($e['time'], 0, 2) . ':' . substr($e['time'], 2). ':00';

                        if ($firstLog === null || $firstLog > $datestamp) {
                            $firstLog = $datestamp;
                        }
                        if ($lastLog === null || $lastLog < $datestamp) {
                            $lastLog = $datestamp;
                        }
                        if ($this->logRepository->checkIfHeardAtPlace($e['signalID'], $heardIn)) {
                            if ($this->logRepository->countTimesHeardByListener($e['signalID'], $id)) {
                                $stats['repeat_for_listener']++;
                            } else {
                                $stats['first_for_listener']++;
                            }
                        } else {
                            $stats['first_for_listener']++;
                            $stats['first_for_place']++;
                        }
                        $e['latest'] = $this->signalRepository->isLatestLogDateAndTime(
                            $e['signalID'],
                            $e['YYYYMMDD'],
                            $e['time']
                        );
                        $stats['latest_for_signal'] += ($e['latest'] ? 1 : 0);
                        $this->logRepository->addLog(
                            $logSessionID,
                            $e['signalID'],
                            $id,
                            $heardIn,
                            $region,
                            $e['YYYYMMDD'],
                            $e['time'],
                            $e['daytime'],
                            $e['dx_km'],
                            $e['dx_miles'],
                            $e['LSB_approx'],
                            $e['LSB'],
                            $e['USB_approx'],
                            $e['USB'],
                            $e['fmt'],
                            $e['sec']
                        );
                        $this->signalRepository->updateSignalStats($e['signalID'], $e['latest']);
                    }
                    $logSession = $this->logsessionRepository->find($logSessionID);
                    $em = $this->getDoctrine()->getManager();
                    if ($firstLog !== null) {
                        $logSession
                            ->setFirstLog(DateTime::createFromFormat('Y-m-d H:i:s', $firstLog) ?? null)
                            ->setLastLog(DateTime::createFromFormat('Y-m-d H:i:s', $lastLog) ?? null)
                            ->setLogs($stats['logs'])
                            ->setLogsDgps($stats['logs_dgps'])
                            ->setLogsDsc($stats['logs_dsc'])
                            ->setLogsHambcn($stats['logs_hambcn'])
                            ->setLogsNavtex($stats['logs_navtex'])
                            ->setLogsNdb($stats['logs_ndb'])
                            ->setLogsOther($stats['logs_other'])
                            ->setLogsTime($stats['logs_time'])
                        ;
                        $em->flush();
                    } else {
                        $em->remove($logSession);
                        $em->flush();
                    }
                    $this->listenerRepository->updateListenerStats($id);
                    $this->listenerRepository->clear();
                    $user->setCountLogSession($user->getCountLogSession() + 1);
                    $user->setCountLog($user->getCountLog() + $stats['logs']);
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    $this->listener = $this->listenerRepository->find($id);
                    $stats['total_signals'] = $this->listener->getCountSignals();
                    $stats['total_logs'] = $this->listener->getCountLogs();
                    break;
            }
        }
        $title = sprintf(
            $this->i18n('Upload Loggings for %s | Step %d'),
            $this->listener->getFormattedNameAndLocation(),
            $step
        );
        $form_logs_height =
            420 - ($this->errors ? 90 + (23 * count($this->errors)) : 0) - ($this->logHas && $this->logHas['partial'] ? 28 : 0);

        if ('UNSET' !== $selected) {
            $_sels = explode(',', $selected);
            $_selected = [];
            foreach($_sels as $_s){
                $_s = explode('|', $_s);
                if (count($_s) === 2) {
                    $_selected[$_s[0]] = $_s[1];
                }
            }
            $selected = $_selected;
        }
        $parameters = [
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'mode' =>               $title,
            'entries' =>            $this->entries,
            'errors' =>             $this->errors,
            'form' =>               $form->createView(),
            'form_logs_height' =>   $form_logs_height,
            'format' =>             $format,
            'formatOld' =>          $this->listener->getLogFormat(),
            'has' =>                $this->logHas,
            'heardIn' =>            $heardIn,
            'logs' =>               $this->listener->getCountLogs(),
            'logData' =>            $this->logs,
            'logEmail' =>           $this->listener->getFormattedEmail(),
            'logOwner' =>           $this->listener->getFormattedNameAndLocation(),
            'region' =>             $region,
            'selected' =>           $selected,
            'signals' =>            $this->listener->getCountSignals(),
            'stats' =>              $stats,
            'step' =>               $step,
            'system' =>             $this->system,
            'tabs' =>               $this->listenerRepository->getTabs($this->listener, $isAdmin),
            'tokens' =>             $this->tokens,
            'tol_offsets' =>        $this->logRepository::TOL_OFFSETS,
            'tol_secs' =>           $this->logRepository::TOL_SECS,
            'userName' =>           $userName
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/logs_upload/index.html.twig', $parameters);
    }

    private function saveFormat($format) {
        $this->listener->setLogFormat($format);
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
