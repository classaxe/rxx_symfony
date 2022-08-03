<?php
namespace App\Controller\Web\Sys;

use App\Controller\Web\Base;
use DateTime;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Sys extends Base
{
    private $response;

    /**
     * @Route(
     *     "/sys/css/{version}/style.css",
     *     defaults={"version"=""},
     *     name="style.css"
     * )
     * @param Packages $assetPackages
     * @return Response
     */
    public function styleCss(Packages $assetPackages)
    {
        $content = new BinaryFileResponse('css/style.css', 200);
        $this->setResponse($content, 'text/css', '+1 day');
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/sys/css/{version}/style.min.css",
     *     defaults={"version"=""},
     *     name="style.min.css"
     * )
     * @param Packages $assetPackages
     * @return Response
     */
    public function styleMinCss(Packages $assetPackages)
    {
        $content = new BinaryFileResponse('css/style.min.css', 200);
        $this->setResponse($content, 'text/css', '+1 day');
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/sys/js/{version}/functions.js",
     *     defaults={"version"=""},
     *     name="functions.js"
     * )
     * @param Packages $assetPackages
     * @return Response
     */
    public function functionsJs(Packages $assetPackages)
    {
        $content = new BinaryFileResponse('js/functions.js', 200);
        $this->setResponse($content, 'application/javascript', '+1 day');
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/sys/js/{version}/functions.min.js",
     *     defaults={"version"=""},
     *     name="functions.min.js"
     * )
     * @param Packages $assetPackages
     * @return Response
     */
    public function functionsJsMin(Packages $assetPackages)
    {
        $content = new BinaryFileResponse('js/functions.min.js', 200);
        $this->setResponse($content, 'application/javascript', '+1 day');
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/sys/js/{version}/i18n.{_locale}.js",
     *     requirements={
     *        "_locale": "de|en|es|fr"
     *     },
     *     defaults={"version"=""},
     *     name="i18n"
     * )
     * @param $_locale
     * @return Response
     */
    public function i18nJs($_locale)
    {
        $content = $this->render('i18n/i18n.js.twig');
        $this->setResponse($content, 'application/javascript', '+1 day');
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/sys/js/listeners.js",
     *     name="js_listeners"
     * )
     * @return Response
     */
    public function listenersJs()
    {
        $data = $this->listenerRepository->getAll(false);
        $content = "var listeners = [\n  \"" . html_entity_decode(implode("\",\n  \"", $data), ENT_NOQUOTES). "\"\n];";
        $this->setResponse(new Response($content, 200), 'application/javascript');
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/sys/js/operators.js",
     *     name="js_operators"
     * )
     * @return Response
     */
    public function operatorsJs()
    {
        $data = $this->listenerRepository->getAll(true);
        $content = "var operators = [\n  \"" . html_entity_decode(implode("\",\n  \"", $data), ENT_NOQUOTES). "\"\n];";
        $this->setResponse(new Response($content, 200), 'application/javascript');
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/sys/js/signals.js",
     *     name="js_signals"
     * )
     * @return Response
     */
    public function signalsJs()
    {
        $data = $this->signalRepository->getAll();
        $content = "var signals = [\n  \"" . html_entity_decode(implode("\",\n  \"", $data), ENT_NOQUOTES) . "\"\n];";
        $this->setResponse(new Response($content, 200), 'application/javascript');
        return $this->getResponse();
    }

    private function getResponse()
    {
        return $this->response;
    }

    private function setResponse(Response $response, $mimeType = false, $expiry = false)
    {
        $this->response = $response;
        if ($mimeType) {
            $this->response->headers->set('Content-Type', $mimeType);
        }
        if ($expiry) {
            $date = new DateTime();
            $date->modify($expiry);
            $this->response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
            $this->response->setExpires($date);
        }
    }
}
