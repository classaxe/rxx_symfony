<?php
namespace App\Controller;

use App\Entity\Region;
use App\Entity\Signals;
use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function listenerListController($system, Rxx $rxx)
    {
        $this->rxx = $rxx;
        $parameters = [
            'system' => $system,
            'mode' => 'Listeners',
            'regions' =>
                $this
                    ->getDoctrine()
                    ->getRepository(Region::class)
                    ->findAll(),
            'text' =>
                 "<ul>\n"
                ."    <li>Log and station counts are updated each time new log data is added - "
                ."figures are for logs in the system at this time.</li>\n"
                ."    <li>To see stats for different types of signals, check the boxes shown for 'Types' below.</li>\n"
                ."    <li>This report prints best in Landscape.</li>\n"
                ."</ul>\n",
            'types' => $this->rxx::types,
            'searchResultText' => "(Showing all 965 listeners)",
            'signal' =>
                $this
                    ->getDoctrine()
                    ->getRepository(Signals::class)
                    ->find(1)
        ];

        return $this->render('listeners/index.html.twig', $parameters);
    }
}