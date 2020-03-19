<?php
namespace App\Controller\Web\Listeners\Export;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/signals/export",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_signals_export"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function redirect_2 ($system, $id)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $system,
            'id' => $id
        ];

        return $this->redirectToRoute('listener_signals_export_csv', $parameters );
    }


    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logs/export",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_logs_export"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function redirect_3 ($system, $id)
    {
        $parameters = [
            '_locale' => $this->get('session')->get('_locale'),
            'system' => $system,
            'id' => $id
        ];

        return $this->redirectToRoute('listener_logs_export_csv', $parameters );
    }
}
