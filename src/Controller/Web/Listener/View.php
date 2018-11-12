<?php
namespace App\Controller\Web\Listener;

use App\Entity\Listener as ListenerEntity;
use App\Form\Listener as ListenerForm;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Rxx;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class View extends Base
{

    /**
     * @Route(
     *     "/{system}/listeners/{id}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener"
     * )
     */
    public function viewController(
        $system,
        $id,
        Request $request,
        ListenerForm $listenerForm,
        ListenerRepository $listenerRepository
    ) {
        if ($id !== 'new' && (int) $id) {
            if (!$listener = $this->getValidListener($id, $listenerRepository)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        } else {
            $listener = false;
        }
        $isAdmin = $this->parameters['isAdmin'];
        if (!$listener && !$isAdmin) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $options = [
            'isAdmin'   =>  $isAdmin,
            'id'        =>  $listener ? $id : '',
            'callsign'  =>  $listener ? $listener->getCallsign() : '',
            'email'     =>  $listener ? $listener->getEmail() : '',
            'equipment' =>  $listener ? $listener->getEquipment() : '',
            'gsq'       =>  $listener ? $listener->getGsq() : '',
            'itu'       =>  $listener ? $listener->getItu() : '',
            'mapX'      =>  $listener ? $listener->getMapX() : '',
            'mapY'      =>  $listener ? $listener->getMapY() : '',
            'name'      =>  $listener ? $listener->getName() : '',
            'notes'     =>  $listener ? $listener->getNotes() : '',
            'primary'   =>  $listener ? $listener->getPrimaryQth() : '',
            'qth'       =>  $listener ? $listener->getQth() : '',
            'sp'        =>  $listener ? $listener->getSp() : '',
            'timezone'  =>  $listener ? $listener->getTimezone() : '',
            'website'   =>  $listener ? $listener->getWebsite() : '',
        ];
        $form = $listenerForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ((int)$id) {
                $listener = $listenerRepository->find($id);
            } else {
                $listener = new ListenerEntity();
                $listener
                    ->setLogLatest(Rxx::getUtcDateTime('0000-00-00'));
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
            $listener
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
            $em = $this->getDoctrine()->getManager();
            if (!(int)$id) {
                $em->persist($listener);
            }
            $em->flush();
            $id = $listener->getId();
            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            'id' =>                 $id,
            'fieldGroups' =>        $listenerForm->getFieldGroups($isAdmin),
            'form' =>               $form->createView(),
            'mode' =>               ($isAdmin && !$listener ? 'Add Listener' : $listener->getName().' &gt; Profile'),
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/profile.html.twig', $parameters);
    }
}
