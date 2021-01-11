<?php
namespace App\Controller\Web\Signals;

use App\Form\Logs\Log as LogForm;
use App\Form\Signals\SignalMerge as SignalMergeForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class SignalMerge extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/{id}/merge",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="signal_merge"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param SignalMergeForm $mergeForm
     * @return RedirectResponse
     */
    public function controller(
        $_locale,
        $system,
        $id,
        $request,
        $mergeForm
    ) {
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('signals', ['_locale' => $_locale, 'system' => $system]);
        }
        $args =  [ '_locale' => $_locale, 'system' => $system ];
        if (!(int) $id) {
            return $this->redirectToRoute('signals', $args);
        }
        if (!$signal = $this->getValidSignal($id)) {
            return $this->redirectToRoute('signals', $args);
        }
        $doReload = $request->query->get('reload') ?? false;

        $options = [
            'id' =>         $signal->getId()
        ];

        $form = $mergeForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);

        /*
                $em = $this->getDoctrine()->getManager();
                $em->remove($signal);
                $em->flush();
        */
        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'doReload' =>           $doReload,
            'form' =>               $form->createView(),
            's' =>                  $signal,
            'mode' =>               'Merge',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('signal/merge.html.twig', $parameters);
    }
}
