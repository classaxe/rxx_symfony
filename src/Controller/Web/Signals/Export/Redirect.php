<?php
namespace App\Controller\Web\Signals\Export;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Redirect
 * @package App\Controller\Web\Signals\Export
 */
class Redirect extends AbstractController
{
    /**
     * @Route(
     *     "/{_locale}/{system}/signals/export",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     * )
     * @param $system
     * @return RedirectResponse
     */
    public function redirect_1 ($system)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $system
        ];

        return $this->redirectToRoute('signals_export_csv', $parameters );
    }
}
