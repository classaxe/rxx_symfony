<?php
namespace App\Controller;

use App\Form\ListenerList as ListenerListForm;
use App\Utils\Rxx;
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
    public function listenerListController($system, Request $request, ListenerListForm $form)
    {
        $options = [
            'matches' =>    '(Showing all <b>961</b> listeners)',
            'system' =>     $system
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
//            print Rxx::y($data);
        }
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
            'form' => $form->createView()
        ];

        return $this->render('listeners/index.html.twig', $parameters);
    }

}