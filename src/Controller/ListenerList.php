<?php
namespace App\Controller;

use App\Form\ListenerList as ListenerListForm;
use App\Utils\Rxx;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListenerList
 * @package App\Controller
 */
class ListenerList extends Controller {

    /**
     * @Route(
     *     "/{system}/listener_list",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_list"
     * )
     */
    public function listenerListController(
        $system, Request $request, ListenerListForm $form, ListenerRepository $listenerRepository
    ) {
        $options = [
            'system' =>     $system
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $arguments = [
            'filter' =>     '',
            'types' =>      [],
            'country' =>    '',
            'region' =>     ''
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $arguments = $form->getData();
//            print Rxx::y($arguments);
        }
        $total = $listenerRepository->getTotalListeners($system);
        $showingAll = (
            empty($arguments['filter']) &&
            empty($arguments['types']) &&
            empty($arguments['country']) &&
            empty($arguments['country'])
        );
        $filtered = $listenerRepository->getFilteredListeners($system, $arguments);
        $matched =
            ($showingAll ?
                "(Showing all $total listeners)"
             :
                "(Showing ".count($filtered)." of $total listeners)"
            );
        $parameters = [
            'system' => $system,
            'mode' => 'Listeners',
            'text' =>
                "<ul>\n"
                ."    <li>Log and station counts are updated each time new log data is added - "
                ."figures are for logs in the system at this time.</li>\n"
                ."    <li>To see stats for different types of signals, check the boxes shown for 'Types' below.</li>\n"
                ."    <li>This report prints best in Landscape.</li>\n"
                ."</ul>\n",
            'form' => $form->createView(),
            'listeners' => $filtered,
            'matched' => $matched
        ];

        return $this->render('listeners/index.html.twig', $parameters);
    }

}