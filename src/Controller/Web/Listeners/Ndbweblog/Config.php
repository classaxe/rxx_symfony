<?php
namespace App\Controller\Web\Listeners\Ndbweblog;

use App\Controller\Web\Listeners\Base;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Ndbweblog
 */
class Config extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/ndbweblog/config.js",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_config"
     * )
     */
    public function configController(
        $_locale,
        $system,
        $id,
        ListenerRepository $listenerRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }
        $parameters = [
            '_locale' =>    $_locale,
            'title' =>      'NDB Weblog config for ' . $listener->getName(),
            'system' =>     $system,
            'listener' =>   $listener
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/ndbweblog/config.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Content-Disposition','attachment;filename=config.js');
        return $response;
    }
}
