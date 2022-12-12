<?php
namespace App\Controller\Web\Signals;

use App\Entity\Signal as SignalEntity;
use App\Form\Signals\SignalView as SignalViewForm;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

class SignalView extends Base
{
    const EDITABLE_FIELDS = [
        'active', 'call', 'decommissioned', 'format', 'gsq', 'itu', 'khz', 'notes', 'pwr', 'qth', 'sec', 'sp', 'type'
    ];

    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param SignalViewForm $signalViewForm
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        SignalViewForm $signalViewForm
    ) {
        if ($id === 'new') {
            $id = false;
        }
        $reloadOpener = false;
        $doReloadOpener = false;
        if ((int) $id) {
            if (!$signal = $this->getValidSignal($id)) {
                return $this->redirectToRoute('signals', ['system' => $system]);
            }
        } else {
            $signal = new SignalEntity();
            if ($request->query->get('data')) {
                $d = json_decode($request->query->get('data'), true);
                $reloadOpener = $d['row'];
                $signal
                    ->setActive(true)
                    ->setType(0)
                    ->setCall($d['ID'])
                    ->setDecommissioned($d['decommissioned'])
                    ->setKhz($d['KHZ'])
                    ->setQth($d['QTH'])
                    ->setSp($d['SP'])
                    ->setItu($d['ITU'])
                    ->setGsq($d['GSQ'])
                    ->setHeardIn('');
            }
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
            'firstHeard' => $signal->getFirstHeard() ? $signal->getFirstHeard()->format('Y-m-d') : '',
            'lastHeard' =>  $signal->getLastHeard() ? $signal->getLastHeard()->format('Y-m-d') : ''
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
            $doReloadOpener = $data['form']['_reload_opener'] ?? false;
            if ((int)$id) {
                $signal = $this->signalRepository->find($id);
            } else {
                $signal = new SignalEntity();
            }
            if ($form_data['gsq']) {
                $GSQ =
                    strtoUpper(substr($form_data['gsq'], 0, 4))
                    .strtoLower(substr($form_data['gsq'], 4, 2));
                $a =    $this->rxx::convertGsqToDegrees($GSQ);
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

            foreach (static::EDITABLE_FIELDS as $f) {
                $signal->{'set' . ucfirst($f)}($form_data[$f]);
            }
            if ($signal->getItu()) {
                $region = $this->countryRepository->getRegionForCountry($signal->getItu());
                $signal->setRegion($region);
            }
            $signal
                ->setGsq($GSQ)
                ->setHeardIn($signal->getHeardIn() ?? '')
                ->setHeardInHtml($signal->getHeardInHtml() ?? '')
                ->setLat($lat)
                ->setLon($lon)
                ->setLsb($lsb)
                ->setLsbApprox($lsbApprox)
                ->setPwr($signal->getPwr() ?? 0)
                ->setUsb($usb)
                ->setUsbApprox($usbApprox);

            $em = $this->getDoctrine()->getManager();
            if (!(int)$id) {
                $em->persist($signal);
            }
            $em->flush();

            if ($form_data['_close']) {
                $js =
                    ($doReloadOpener ?
                        "window.opener.document.getElementById('form_selected').value = "
                        . "window.opener.document.getElementById('form_selected').value + ',"
                        . explode('_', $reloadOpener)[1] . "|" . $signal->getId() . "';"
                        . "window.opener.document.getElementsByName('form')[0].submit();" : ''
                    )
                    . "window.close()";
                return new Response("<script>$js</script>", Response::HTTP_OK, [ 'content-type' => 'text/html' ]);
            }

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
            'mode' =>
                ($isAdmin && !$id ?
                    $this->i18n('Add Signal')
                :
                    sprintf($signal->getActive() ?
                        $this->i18n('Details for %s (Active)')
                    :
                        $this->i18n('Details for %s (Inctive)'),  $signal->getFormattedIdent()
                    )
                ),
            'doReloadOpener' =>     $doReloadOpener,
            'morse' =>              $signal->getFormattedMorse(),
            'reloadOpener' =>       $reloadOpener,
            'system' =>             $system,
            'tabs' =>               $this->signalRepository->getTabs($signal),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/profile.html.twig', $parameters);
    }
}
