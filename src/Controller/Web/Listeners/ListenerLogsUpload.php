<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\LogUpload as LogUploadForm;
use App\Utils\Rxx;
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
    private $comment;
    private $entries = [];
    private $errors = [];
    private $listener;
    private $logs;
    private $logHas;
    private $operator = null;
    private $operatorId = false;
    private $tokens = [];
    private $system;
    private $YYYY;
    private $MM;
    private $DD;

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
        $userName = $this->parameters['user_name'];
        $heardIn =  $this->listener->getHeardIn();
        $region =   $this->listener->getRegion();
        $isMulti =  $this->listener->getMultiOperator() === 'Y';
        $step =     $request->get('step', '1');
        $format =   $this->listener->getLogFormat();
        $selected = 'UNSET';
        $options = [
            'id' =>         $this->listener->getId(),
            'step' =>       $step,
            'selected' =>   $selected,
            'format' =>     $format,
            'comment' =>    '',
            'system' =>     $system,
            'operatorId' => $this->operatorId
        ];
        $stats = [
            'duplicates' => 0,
            'grouped' => 0,
            'first_log' => null,
            'last_log' => null,
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
            'signals' => 0,
            'repeat_for_listener' => 0
        ];

        $form = $logUploadForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $data =             $form->getData();
            $step =             $data['step'];
            $format =           $data['format'];
            $this->logs =       $data['logs'];
            $this->operatorId = $data['operatorId'];
            $this->operator =   ($this->operatorId ? $this->getValidListener($this->operatorId) : null);
            $selected =         $data['selected'];
            $this->errors =     [];
            $this->logRepository->parseFormat($format, $this->tokens, $this->errors, $this->logHas);

            $this->YYYY =       $this->logHas['YYYY'] ? '' : $data['YYYY'];
            $this->MM =         $this->logHas['MM']   ? '' : $data['MM'];
            $this->DD =         $this->logHas['DD']   ? '' : $data['DD'];
            $this->comment =    $data['comment'];

            switch ($step) {
                case '1b':
                    if (!$this->errors && !$this->logHas['partial']) {
                        $this->saveFormat($data['format']);
                    }
                    $step = '1';
                    break;
                case '2':
                    if ($this->errors
                        || (!$this->logHas['YYYY'] && !$this->YYYY)
                        || (!$this->logHas['MM'] && !$this->MM)
                        || (!$this->logHas['DD'] && !$this->DD)
                        || (!$this->operatorId && $this->listener->getMultiOperator() === 'Y')
                    ) {
                        $step = '1';
                        break;
                    }
                    $this->entries = $this->parseLog(false);
                    break;
                case '3':
                    if ($this->errors
                        || (!$this->logHas['YYYY'] && !$this->YYYY)
                        || (!$this->logHas['MM'] && !$this->MM)
                        || (!$this->logHas['DD'] && !$this->DD)
                    ) {
                        $step = '1';
                        break;
                    }
                    $this->entries =    $this->parseLog($selected);
                    $this->user =       $this->userRepository->find($this->session->get('user_id'));

                    $logSessionID =     $this->createLogSession();

                    $stats = $this->processBatch($logSessionID);
//                    $stats = $this->processBatch();
                    break;
            }
        }
        $title = sprintf(
            $this->i18n('Upload Logs | %s | Step %d'),
            $this->listener->getFormattedNameAndLocation(),
            $step
        );
        $form_logs_height = 420
            - ($this->errors ? 90 + (23 * count($this->errors)) : 0)
            - ($this->logHas && $this->logHas['partial'] ? 28 : 0)
            - ($isMulti ? 64 : 0);

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
            'cle' =>                $this->cleRepository->find(1),
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
            'multiOperator' =>      $this->listener->getMultiOperator() === 'Y',
            'operatorId' =>         $this->operatorId,
            'operator' =>           $this->operator,
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

    private function createLogSession() {
        $stats = [
            'duplicates' => 0,
            'grouped' => 0,
            'first_log' => null,
            'last_log' => null,
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
            'signals' => 0,
            'signalIds' => [],
            'repeat_for_listener' => 0
        ];
        return $this->logsessionRepository->addLogSession(
            new DateTime(),
            $this->user->getId(),
            $this->listener->getId(),
            ($this->operatorId ? (int)$this->operatorId : null),
            $this->comment,
            $this->entries,
            $stats,
            'Pending'
        );
    }

    private function getProcessSession() {
        $ls = $this->logsessionRepository->findOneBy(['uploadStatus' => 'Pending'], ['id' => 'ASC']);
        if (!$ls) {
            return false;
        }
        return $ls->getId();
    }

    private function parseLog($selected) {
        return $this->logRepository->parseLog(
            $this->listener,
            $this->logs,
            $this->tokens,
            $this->YYYY,
            $this->MM,
            $this->DD,
            $this->signalRepository,
            $selected
        );
    }

    // This must ONLY use data saved in the saved log_session record since it now processes offline
    private function processBatch($sessionID = false) {
        if (!$sessionID) {
            $sessionID = $this->getProcessSession();
        }
        if (!$sessionID) {
            return false;
        }
        $ls = $this->logsessionRepository->find($sessionID);
        if (!$ls) {
            return false;
        }
        $stats =        unserialize($ls->getUploadStats());
        $entries =      unserialize($ls->getUploadEntries());
        $adminID =      $ls->getAdministratorId();
        $listenerID =   $ls->getListenerId();
        $operatorId =   $ls->getOperatorId();
        $admin =        $this->userRepository->find($adminID);
        $listener =     $this->listenerRepository->find($listenerID);
        $heardIn =      $listener->getHeardIn();
        $region =       $listener->getRegion();

        foreach($entries as $e) {
            $isPresent = false;
            if ($row = $this->logRepository->findDuplicate($e['signalID'], $listenerID, $e['YYYYMMDD'], $e['time'])) {
                if ($operatorId === $row['operatorId']) {
                    $stats['duplicates']++;
                } else {
                    $log = $this->logRepository->find($row['ID']);
                    $log->setLogSessionId($sessionID)
                        ->setOperatorId($operatorId ? (int)$operatorId : null);
                    $this->getDoctrine()->getManager()->flush();
                    $stats['grouped']++;
                }
                $isPresent = true;
            }
            $stats['logs']++;
            $type = $this->typeRepository->getTypeForCode($e['type']);
            $stats['logs_' . strtolower($type['class'])]++;
            $datestamp = $e['YYYYMMDD'] . ' ' . substr($e['time'], 0, 2) . ':' . substr($e['time'], 2). ':00';

            if ($stats['first_log'] === null || $stats['first_log'] > $datestamp) {
                $stats['first_log'] = $datestamp;
            }
            if ($stats['last_log'] === null || $stats['last_log'] < $datestamp) {
                $stats['last_log'] = $datestamp;
            }
            if ($this->logRepository->checkIfHeardAtPlace($e['signalID'], $heardIn)) {
                if ($this->logRepository->countTimesHeardByListener($e['signalID'], $listenerID)) {
                    $stats['repeat_for_listener']++;
                } else {
                    $stats['first_for_listener']++;
                }
            } else {
                $stats['first_for_listener']++;
                $stats['first_for_place']++;
            }
            $stats['signalIds'][] = $e['signalID'];
            $e['latest'] = $this->signalRepository->isLatestLogDateAndTime(
                $e['signalID'],
                $e['YYYYMMDD'],
                $e['time']
            );
            $stats['latest_for_signal'] += ($e['latest'] ? 1 : 0);
            if (!$isPresent) {
                $this->logRepository->addLog(
                    $sessionID,
                    $e['signalID'],
                    $listenerID,
                    ($operatorId ? $operatorId : null),
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
        }
        $logSession = $this->logsessionRepository->find($sessionID);
        $em = $this->getDoctrine()->getManager();
        $stats['signals'] = count(array_unique($stats['signalIds']));
        if ($stats['first_log'] !== null) {
            $logSession
                ->setFirstLog(DateTime::createFromFormat('Y-m-d H:i:s', $stats['first_log']) ?? null)
                ->setLastLog(DateTime::createFromFormat('Y-m-d H:i:s', $stats['last_log']) ?? null)
                ->setLogs($stats['logs'])
                ->setLogsDgps($stats['logs_dgps'])
                ->setLogsDsc($stats['logs_dsc'])
                ->setLogsHambcn($stats['logs_hambcn'])
                ->setLogsNavtex($stats['logs_navtex'])
                ->setLogsNdb($stats['logs_ndb'])
                ->setLogsOther($stats['logs_other'])
                ->setLogsTime($stats['logs_time'])
                ->setSignals($stats['signals'])
                ->setUploadStatus('Uploaded')
                ->setUploadPercent(100)
            ;
            $em->flush();
        } else {
            $em->remove($logSession);
            $em->flush();
        }

        $this->listenerRepository->updateListenerStats($listenerID);
        if ($operatorId) {
            $this->listenerRepository->updateListenerStats($operatorId);
        }
        $this->listenerRepository->clear();
        $admin->setCountLogSession($admin->getCountLogSession() + 1);
        $admin->setCountLog($admin->getCountLog() + $stats['logs']);
        $this->getDoctrine()->getManager()->flush();

        $listener = $this->listenerRepository->find($listenerID);
        $stats['total_signals'] = $listener->getCountSignals();
        $stats['total_logs'] = $listener->getCountLogs();
        $ls->setUploadStats(serialize($stats));
        $this->getDoctrine()->getManager()->flush();
        return $stats;
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
