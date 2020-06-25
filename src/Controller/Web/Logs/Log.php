<?php
namespace App\Controller\Web\Logs;

use App\Controller\Web\Base as WebBase;
use App\Form\Logs\Log as LogForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Rxx;

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
            'usbApprox' =>  $log->getUsbApprox() ? true : false,
        ];
//        print "<pre>" . print_r($options, true) . "</pre>";
        $form = $logForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ($form_data['gsq'] && $a = Rxx::convertGsqToDegrees($form_data['gsq'])) {
                $lat =  $a["lat"];
                $lon =  $a["lon"];
                $GSQ =  $a["GSQ"];
            } else {
                $GSQ =  '';
                $lat =  0;
                $lon =  0;
            }
            $region = $countryRepository->getRegionForCountry($form_data['itu']);
            $listener
                ->setGsq($GSQ)
                ->setLat($lat)
                ->setLon($lon)
                ->setRegion($region);
            foreach (static::EDITABLE_FIELDS as $f) {
                $listener->{'set' . ucfirst($f)}($form_data[$f]);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($listener);
            $em->flush();
            $id = $listener->getId();

            if ($form_data['_close']) {
                return new Response(
                    '<script>window.close();</script>',
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                );
            }

            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'form' =>               $form->createView(),
            'l' =>                  $log,
            'mode' =>               'Edit Log',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('log/edit.html.twig', $parameters);
    }
}
