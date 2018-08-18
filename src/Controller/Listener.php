<?php
namespace App\Controller;

use App\Entity\Listener as ListenerEntity;
use App\Form\Listener as ListenerForm;
use App\Repository\ListenerRepository;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller
 */
class Listener extends Base
{

    /**
     * @Route(
     *     "/{system}/listener/{id}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener"
     * )
     */
    public function listenerController(
        $system,
        $id,
        Request $request,
        ListenerForm $listenerForm,
        ListenerRepository $listenerRepo
    ) {
        if ((int) $id) {
            $listener = $listenerRepo->find((int)$id);
            if (!$listener) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        }
        $isAdmin = $this->parameters['isAdmin'];
        $options = [
            'isAdmin'   =>  $isAdmin,
            'id'        =>  $id,
            'callsign'  =>  $id ? $listener->getCallsign() : '',
            'email'     =>  $id ? $listener->getEmail() : '',
            'equipment' =>  $id ? $listener->getEquipment() : '',
            'gsq'       =>  $id ? $listener->getGsq() : '',
            'itu'       =>  $id ? $listener->getItu() : '',
            'mapX'      =>  $id ? $listener->getMapX() : '',
            'mapY'      =>  $id ? $listener->getMapY() : '',
            'name'      =>  $id ? $listener->getName() : '',
            'notes'     =>  $id ? $listener->getNotes() : '',
            'primary'   =>  $id ? $listener->getPrimaryQth() : '',
            'qth'       =>  $id ? $listener->getQth() : '',
            'sp'        =>  $id ? $listener->getSp() : '',
            'timezone'  =>  $id ? $listener->getTimezone() : '',
            'website'   =>  $id ? $listener->getWebsite() : '',
        ];
        $form = $listenerForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ($id) {
                $listener = $listenerRepo->find($id);
            } else {
                $listener = new ListenerEntity();
                $listener
                    ->setLogLatest(\App\Utils\Rxx::getUtcDateTime('0000-00-00'));
            }
            $listener
                ->setCallsign($form_data['callsign'])
                ->setEmail($form_data['email'])
                ->setEquipment($form_data['equipment'])
                ->setGsq($form_data['gsq'])
                ->setItu($form_data['itu'])
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
            if (!$id) {
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
            'mode' =>               ($isAdmin ? ($id ? 'Edit' : 'Add').' Listener' : 'Listener Details'),
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/edit.html.twig', $parameters);
    }
}
