<?php
namespace App\Controller;

use App\Entity\Listener as ListenerEntity;
use App\Form\Listener as ListenerForm;
use App\Repository\ListenerRepository;
use App\Utils\Rxx;
use Doctrine\ORM\EntityManagerInterface;

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
        $isAdmin = $this->parameters['isAdmin'];
        $listener =
            $listenerRepo
                ->find($id);
        if (!$listener) {
            $id = null;
        }
        $options = [
            'isAdmin'   =>  $isAdmin,
            'id'        =>  $id,
            'callsign'  =>  $id ? $listener->getCallsign() : '',
            'email'     =>  $id ? $listener->getEmail() : '',
            'gsq'       =>  $id ? $listener->getGsq() : '',
            'itu'       =>  $id ? $listener->getItu() : '',
            'name'      =>  $id ? $listener->getName() : '',
            'qth'       =>  $id ? $listener->getQth() : '',
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
            $listener = $listenerRepo->find($id);
            $listener->setEmail($form_data['email']);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        $parameters = [
            'id' =>                 $id,
            'fieldGroups' =>        $listenerForm->getFieldGroups($isAdmin),
            'form' =>               $form->createView(),
            'mode' =>               ($isAdmin ? ($id ? 'Edit' : 'Add').' Listener' : 'Listener Details'),
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listeners/edit.html.twig', $parameters);
    }
}
