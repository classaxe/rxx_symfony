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
        if (!$this->parameters['isAdmin'] || !$log = $this->logRepository->find($id)) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $doReload = $request->query->get('reload') ?? false;

        $options = [
            'id' =>         $log->getId(),
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
            $oldSignalId =      $options['signalId'];
            $newListenerId =    $data['listenerId'];
            $newSignalId =      $data['signalId'];
            $listener =         $this->listenerRepository->find($data['listenerId']);
            $signal =           $this->signalRepository->find($data['signalId']);
            $daytime =          $listener->isDaytime($data['time']);
            $heardIn =          $listener->getSp() ?? $listener->getItu();
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
                ->setLsbApprox($data['lsbApprox'])
                ->setHeardIn($heardIn)
                ->setRegion($region)
                ->setSec($data['sec'])
                ->setTime($data['time'])
                ->setUsb($data['usb']!=='' ? (int)$data['usb'] : null)
                ->setUsbApprox($data['usbApprox']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($log);
            $em->flush();

            $this->listenerRepository->updateListenerStats($newListenerId);
            if ($oldListenerId !== $newListenerId) {
                $this->listenerRepository->updateListenerStats($oldListenerId);
            }

            $this->signalRepository->updateSignalStats($newSignalId, true);
            if ($oldSignalId !== $newSignalId) {
                $this->signalRepository->updateSignalStats($oldSignalId, true);
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
            'mode' =>               'Edit Log',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('log/edit.html.twig', $parameters);
    }
}
