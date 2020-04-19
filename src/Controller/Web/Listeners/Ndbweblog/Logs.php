<?php
namespace App\Controller\Web\Listeners\Ndbweblog;

use App\Controller\Web\Listeners\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Ndbweblog
 */
class Logs extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/ndbweblog/logs.js",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_logs"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id
    ) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute(
                'listeners',
                ['system' => $system]
            );
        }
        $parameters = [
            '_locale' =>            $_locale,
            'title' =>              'NDB Weblog logs for '.$listener->getName(),
            'system' =>             $system,
            'listener' =>           $listener,
            'logs' =>               $this->logRepository->getLogsForListener($id)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        $response = $this->render('listener/ndbweblog/logs.js.twig', $parameters);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Content-Disposition','attachment;filename=logs.js');

        return $response;
    }
}
