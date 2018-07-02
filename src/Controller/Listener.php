<?php
namespace App\Controller;

use App\Form\Listener as ListenerForm;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListenerList
 * @package App\Controller
 */
class Listener extends BaseController {

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
        ListenerForm $form,
        ListenerRepository $listenerRepository
    ) {
        $options = [
            'system' =>     $system,
            'id' =>         $id
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $parameters = [
            'id' =>                 $id,
            'form' =>               $form->createView(),
            'mode' =>               'Edit Listener',
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listeners/edit.html.twig', $parameters);
    }

}