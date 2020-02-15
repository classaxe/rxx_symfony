<?php
namespace App\Controller\Web\Listeners\Export;

use App\Service\GeoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Listeners\Export
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/{system}/listeners/{id}/signalmap",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     * )
     * @param $system
     * @param $id
     * @return RedirectResponse
     */
    public function redirect_1 ($system, $id)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $system,
            'id' => $id
        ];

        return $this->redirectToRoute('listener_export_signalmap', $parameters );
    }
}
