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
    const EDITABLE_FIELDS = [
        'active', 'call', 'format', 'gsq', 'itu', 'khz', 'notes', 'pwr', 'qth', 'sec', 'sp', 'type'
    ];

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
            'isAdmin' =>    $isAdmin,
            'id' =>         $signal->getId(),
            'heardIn' =>    $signal->getHeardIn(),
            'lat' =>        $signal->getLat(),
            'lon' =>        $signal->getLon(),
            'lsb' =>        $signal->getLsbApprox() . $signal->getLsb(),
            'usb' =>        $signal->getUsbApprox() . $signal->getUsb(),
            'firstHeard' => $signal->getFirstHeard()->format('Y-m-d'),
            'lastHeard' =>  $signal->getLastHeard()->format('Y-m-d')
        ];
        foreach (static::EDITABLE_FIELDS as $f) {
            $options[$f] = $signal->{'get' . ucfirst($f)}();
        }
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
            $lsbApprox = substr($form_data['lsb'], 0, 1) === '~' ? '~' : null;
            $usbApprox = substr($form_data['usb'], 0, 1) === '~' ? '~' : null;
            $lsb = str_replace('~', '', $form_data['lsb']);
            $usb = str_replace('~', '', $form_data['usb']);
            $signal
                ->setGsq($GSQ)
                ->setLat($lat)
                ->setLon($lon)
                ->setLsb($lsb)
                ->setLsbApprox($lsbApprox)
                ->setUsb($usb)
                ->setUsbApprox($usbApprox);

            foreach (static::EDITABLE_FIELDS as $f) {
                $signal->{'set' . ucfirst($f)}($form_data[$f]);
            }
            $em = $this->getDoctrine()->getManager();
            if (!(int)$id) {
                $em->persist($signal);
            }
            $em->flush();
            $id = $signal->getId();
            return $this->redirectToRoute(
                'signal',
                [ 'system' => $system, 'id' => $id]
            );
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
