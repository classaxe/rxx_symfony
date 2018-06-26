<?php
namespace App\Controller;

use App\Form\ListenerList as ListenerListForm;
use App\Repository\ListenerRepository;
use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListenerList
 * @package App\Controller
 */
class Logon extends BaseController {

    /**
     * @Route(
     *     "/{system}/logon",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="logon"
     * )
     */
    public function logonController(
        $system,
        Request $request,
        ListenerListForm $form,
        ListenerRepository $listenerRepository
    ) {
        if (!$this->session->get('isAdmin', 0)) {
            $this->session->set('isAdmin', 1);
            return $this->redirectToRoute('logon', ['system' => $system]);
        }
        $options = [
            'system' =>     $system
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'filter' =>     '',
            'types' =>      [],
            'country' =>    '',
            'region' =>     '',
            'sort' =>       'name',
            'order' =>      'a'
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
//            print $this->rxx::y($args);
        }
        $total = $listenerRepository->getTotalListeners($system);
        $showingAll = (
            empty($args['filter']) &&
            empty($args['country']) &&
            empty($args['region'])
        );
        if (empty($args['types'])) {
            $args['types'][] = 'type_NDB';
        }
        $filtered = $listenerRepository->getFilteredListeners($system, $args);
        $matched =
            ($showingAll ?
                "(Showing all $total listeners)"
             :
                "(Showing ".count($filtered)." of $total listeners)"
            );
        $parameters = [
            'args' =>       $args,
            'columns' =>    $listenerRepository->getColumns(),
            'form' =>       $form->createView(),
            'listeners' =>  $filtered,
            'matched' =>    $matched,
            'mode' =>       'Listeners List',
            'system' =>     $system,
            'text' =>
                "<ul>\n"
                ."    <li>Log and station counts are updated each time new log data is added - "
                ."figures are for logs in the system at this time.</li>\n"
                ."    <li>To see stats for different types of signals, check the boxes shown for 'Types' below.</li>\n"
                ."    <li>This report prints best in Portrait.</li>\n"
                ."</ul>\n",
        ];
        $parameters = array_merge($parameters, $this->parameters);
//        return $this->rxx::debug($this->parameters);
        return $this->render('listeners/index.html.twig', $parameters);
    }

}