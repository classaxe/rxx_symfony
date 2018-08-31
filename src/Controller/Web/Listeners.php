<?php
namespace App\Controller\Web;

use App\Form\Listeners as ListenerListForm;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Listeners extends Base
{

    /**
     * @Route(
     *     "/{system}/listeners",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listeners"
     * )
     */
    public function listenerListController(
        $system,
        Request $request,
        ListenerListForm $form,
        ListenerRepository $listenerRepository
    ) {
        $options = [
            'system' =>     $system,
            'region' =>     (isset($_REQUEST['form']['region']) ? $_REQUEST['form']['region'] : '')
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
        $listeners = $listenerRepository->getFilteredListeners($system, $args);
        $matched =
            ($showingAll ?
                "(Showing all $total listeners)"
             :
                "(Showing ".count($listeners)." of $total listeners)"
            );
        $parameters = [
            'args' =>               $args,
            'columns' =>            $listenerRepository->getColumns(),
            'form' =>               $form->createView(),
            'listeners' =>          $listeners,
            'matched' =>            $matched,
            'mode' =>               'Listeners List',
            'listenerPopup' =>      'width=590,height=640,status=1,scrollbars=1,resizable=1',
            'system' =>             $system,
            'text' =>
                "<ul>\n"
                ."    <li>Log and station counts are updated each time new log data is added - "
                ."figures are for logs in the system at this time.</li>\n"
                ."    <li>To see stats for different types of signals, check the boxes shown for 'Types' below.</li>\n"
                ."    <li>This report prints best in Portrait.</li>\n"
                ."</ul>\n",
        ];
        if ($this->parameters['isAdmin']) {
            $parameters['latestListeners'] =    $listenerRepository->getLatestLoggedListeners($system);
            $parameters['latestLogs'] =         $listenerRepository->getLatestLogs($system);
        }
        return $this->render('listeners/index.html.twig', $this->getMergedParameters($parameters));
    }
}
