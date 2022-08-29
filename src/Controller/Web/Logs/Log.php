<?php
namespace App\Controller\Web\Logs;

use App\Controller\Web\Base as WebBase;
use App\Form\Logs\Log as LogForm;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web\Log
 */
class Log extends WebBase
{
    /**
     * @Route(
     *     "/{_locale}/{system}/logs/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="log"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param LogForm $logForm
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        LogForm $logForm
    ) {
        if (!$log = $this->logRepository->find($id)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $isAdmin = $this->parameters['isAdmin'];
        $doReload = $request->query->get('reload') ?? false;

        $options = [
            'id' =>         $log->getId(),
            'isAdmin' =>    $isAdmin,
            'sessionId' =>  $log->getLogSessionId(),
            'signalId' =>   $log->getSignalId(),
            'date' =>       $log->getDate() ? $log->getDate()->format('Y-m-d') : '',
            'daytime' =>    $log->getDaytime(),
            'dxKm' =>       $log->getDxKm(),
            'dxMiles' =>    $log->getDxMiles(),
            'format' =>     $log->getFormat(),
            'heardIn' =>    $log->getHeardIn(),
            'listenerId' => $log->getListenerId(),
            'lsb' =>        $log->getLsb(),
            'lsbApprox' =>  $log->getLsbApprox() ? true : false,
            'operatorId' => $log->getOperatorId(),
            'region' =>     $log->getRegion(),
            'sec' =>        $log->getSec(),
            'time' =>       $log->getTime(),
            'usb' =>        $log->getUsb(),
            'usbApprox' =>  $log->getUsbApprox() ? true : false
        ];
//        print "<pre>" . print_r($options, true) . "</pre>";
        $form = $logForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data =             $form->getData();
            $oldListenerId =    $options['listenerId'];
            $oldOperatorId =    $options['operatorId'];
            $oldSignalId =      $options['signalId'];
            $oldTime =          $options['time'];
            $newListenerId =    $data['listenerId'];
            $newOperatorId =    $data['operatorId'];
            $newSignalId =      $data['signalId'];
            $newTime =          $data['time'];
            $listener =         $this->listenerRepository->find($data['listenerId']);
            $signal =           $this->signalRepository->find($data['signalId']);
            $daytime =          $listener->isDaytime($data['time']);
            $heardIn =          $listener->getSp() ? $listener->getSp() : $listener->getItu();
            $region =           $listener->getRegion();
            $dx =               $this->rxx->getDxGsq2Gsq($listener->getGsq(), $signal->getGsq());
            $log
                ->setSignalId($data['signalId'])
                ->setDate(DateTime::createFromFormat('Y-m-d', $data['date']))
                ->setDaytime($daytime)
                ->setDxKm($dx['km'])
                ->setDxMiles($dx['miles'])
                ->setFormat($data['format'])
                ->setListenerId($data['listenerId'])
                ->setLsb($data['lsb']!=='' ? (int)$data['lsb'] : null)
                ->setLsbApprox(!empty($request->request->get('form')['lsbApprox']))
                ->setOperatorId($data['operatorId'] ? (int)$data['operatorId'] : null)
                ->setHeardIn($heardIn)
                ->setRegion($region)
                ->setSec($data['sec'])
                ->setTime($data['time'])
                ->setUsb($data['usb']!=='' ? (int)$data['usb'] : null)
                ->setUsbApprox(!empty($request->request->get('form')['usbApprox']));

            $em = $this->getDoctrine()->getManager();
            $em->persist($log);
            $em->flush();

            // Listener Stats
            if ($oldListenerId !== $newListenerId || $oldTime !== $newTime) {
                // Log belongs to another location, or daytime / nighttime designation may have changed
                $this->listenerRepository->updateListenerStats($oldListenerId);
                $this->listenerRepository->updateListenerStats($newListenerId);
            }

            // Operator Stats
            if ((int)$oldOperatorId !== (int)$newOperatorId) {
                // Update old and new operator stats
                if ((int)$oldOperatorId) {
                    $this->listenerRepository->updateListenerStats($oldOperatorId);
                }
                if ((int)$newOperatorId) {
                    $this->listenerRepository->updateListenerStats($newOperatorId);
                }
            }

            // Signal Stats
            if ($oldListenerId !== $newListenerId || $oldSignalId !== $newSignalId || $oldTime !== $newTime) {
                // Signal, location or possibly day / night designations have changed
                $this->signalRepository->updateSignalStats($oldSignalId, true, true);
                if ($oldSignalId !== $newSignalId || $oldTime !== $newTime) {
                    $this->signalRepository->updateSignalStats($newSignalId, true, true);
                }
            }

            if ($log->getLogSessionId()) {
                $this->listenerRepository->updateStats((int) $log->getLogSessionId());
            }

            if ($data['_close']) {
                $js =
                    ($doReload ?
                        "window.opener.document.getElementsByName('form')[0].submit();" : ''
                    )
                    . "window.close()";
                return new Response(
                    "<script>$js</script>",
                    Response::HTTP_OK,
                    [ 'content-type' => 'text/html' ]
                );
            }

            return $this->redirectToRoute('log', ['system' => $system, 'id' => $id, 'reload' =>1]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'doReload' =>           $doReload,
            'form' =>               $form->createView(),
            'l' =>                  $log,
            'mode' =>               'Log Entry',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('log/view.html.twig', $parameters);
    }
}
