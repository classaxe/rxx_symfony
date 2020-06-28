<?php
namespace App\Controller\Web\Sys;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Js extends Base
{
    /**
     * @Route(
     *     "/{_locale}/sysjs/i18n",
     *     requirements={
     *        "_locale": "de|en|es|fr"
     *     },
     *     name="i18n"
     * )
     * @param $_locale
     * @return Response
     */
    public function jsI18n($_locale)
    {
        $response = $this->render('i18n/i18n.js.twig');
        $response->headers->set('Content-Type', 'application/javascript');
        return $response;
    }

    /**
     * @Route(
     *     "/sysjs/listeners",
     *     name="js_listeners"
     * )
     * @return Response
     */
    public function listeners()
    {
        $data = $this->listenerRepository->getAll();
        $out = "var listeners = [\n  \"" . html_entity_decode(implode("\",\n  \"", $data), ENT_NOQUOTES). "\"\n];";
        $response = new Response($out, 200);
        $response->headers->set('Content-Type', 'application/javascript');
        return $response;
    }

    /**
     * @Route(
     *     "/sysjs/signals",
     *     name="js_signals"
     * )
     * @return Response
     */
    public function signals()
    {
        $data = $this->signalRepository->getAll();
        $out = "var signals = [\n  \"" . html_entity_decode(implode("\",\n  \"", $data), ENT_NOQUOTES) . "\"\n];";
        $response = new Response($out, 200);
        $response->headers->set('Content-Type', 'application/javascript');
        return $response;
    }
}
