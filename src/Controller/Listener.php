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
 * Class ListenerList
 * @package App\Controller
 */
class Listener extends BaseController
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
        ListenerRepository $listenerRepository
    ) {
        $listener_Repo =
            $this
                ->getDoctrine()
                ->getRepository(ListenerEntity::class);
        $listener =
            $listener_Repo
                ->find($id);
        if (!$listener) {
            $id = null;
        }
        $options = [
            'id' =>         $id,
            'callsign' =>   $id ? $listener->getCallsign() : '',
            'email' =>      $id ? $listener->getEmail() : '',
            'itu' =>        $id ? $listener->getItu() : '',
            'name' =>       $id ? $listener->getName() : '',
            'website' =>    $id ? $listener->getWebsite() : '',
        ];

        $form = $listenerForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            $listener = $listener_Repo->find($id);
            $listener
                ->setEmail($form_data['email']);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        $parameters = [
            'id' =>                 $id,
            'fieldGroups' =>        $listenerForm->getFieldGroups(),
            'form' =>               $form->createView(),
            'mode' =>               'Edit Listener',
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listeners/edit.html.twig', $parameters);
    }
}
