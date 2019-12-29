<?php
namespace App\Controller\Web\Signals;

use App\Entity\Signal as SignalEntity;
use App\Form\Signals\SignalView as SignalViewForm;
use App\Repository\SignalRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Rxx;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class SignalView extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal"
     * )
     */
    public function viewController(
        $_locale,
        $system,
        $id,
        Request $request,
        SignalViewForm $signalViewForm,
        SignalRepository $signalRepository
    ) {
        if ($id === 'new') {
            $id = false;
        }
        if ((int) $id) {
            if (!$signal = $this->getValidSignal($id, $signalRepository)) {
                return $this->redirectToRoute('signals', ['system' => $system]);
            }
        } else {
            $signal = new SignalEntity();
        }
        $isAdmin = $this->parameters['isAdmin'];
        if (!$id && !$isAdmin) {
            return $this->redirectToRoute('signals', ['system' => $system]);
        }
        $options = [
            'isAdmin' =>  $isAdmin,
            'id' =>     $signal->getId(),
            'call' =>   $signal->getCall(),
            'khz' =>    $signal->getKhz(),
            'pwr' =>    $signal->getPwr(),
            'type' =>   $signal->getType(),
            'qth' =>    $signal->getQth(),
            'sp' =>     $signal->getSp(),
            'itu' =>    $signal->getItu(),
            'gsq' =>    $signal->getGsq(),
            'heardIn' =>$signal->getHeardIn(),
            'lat' =>    $signal->getLat(),
            'lon' =>    $signal->getLon(),
            'lsb' =>    $signal->getLsb(),
            'usb' =>    $signal->getUsb(),
            'sec' =>    $signal->getSec(),
            'active' => $signal->getActive(),
            'format' => $signal->getFormat(),
            'firstHeard' => $signal->getFirstHeard()->format('Y-m-d'),
            'lastHeard' => $signal->getLastHeard()->format('Y-m-d'),
            'notes' =>  $signal->getNotes(),
        ];
        $form = $signalViewForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ((int)$id) {
                $signal = $signalRepository->find($id);
            } else {
                $signal = new SignalEntity();
            }
            if ($form_data['gsq']) {
                $GSQ =
                    strtoUpper(substr($form_data['gsq'], 0, 4))
                    .strtoLower(substr($form_data['gsq'], 4, 2));
                $a =    Rxx::convertGsqToDegrees($GSQ);
                $lat =  $a["lat"];
                $lon =  $a["lon"];
            } else {
                $GSQ =  '';
                $lat =  0;
                $lon =  0;
            }
/*
            $signal
                ->setCallsign($form_data['callsign'])
                ->setEmail($form_data['email'])
                ->setEquipment($form_data['equipment'])
                ->setGsq($GSQ)
                ->setItu($form_data['itu'])
                ->setLat($lat)
                ->setLon($lon)
                ->setMapX($form_data['mapX'])
                ->setMapY($form_data['mapY'])
                ->setName($form_data['name'])
                ->setNotes($form_data['notes'])
                ->setPrimaryQth($form_data['primary'])
                ->setQth($form_data['qth'])
                ->setSp($form_data['sp'])
                ->setTimezone($form_data['timezone'])
                ->setWebsite($form_data['website'])
            ;

*/            $em = $this->getDoctrine()->getManager();
            if (!(int)$id) {
                $em->persist($signal);
            }
            $em->flush();
            $id = $signal->getId();
            return $this->redirectToRoute('signal', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            'id' =>                 $id,
            'heardInHtml' =>        $signal->getHeardInHtml(),
            'isAdmin' =>            $isAdmin,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'mode' =>               $isAdmin && !$signal ? 'Add Signal' : $signal->getCall() . '-' . $signal->getKhz(). ' (' . ($signal->getActive() ? 'Active' : 'Inactive') . ')',
            'system' =>             $system,
            'tabs' =>               $signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/profile.html.twig', $parameters);
    }
}
