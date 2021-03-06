<?php
namespace App\Controller\Web\Listeners\Ndbweblog;

use App\Controller\Web\Listeners\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listener\Ndbweblog
 */
class View extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/ndbweblog",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog"
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
            'id' =>                 $id,
            '_locale' =>            $_locale,
            'title' =>              'NDB Weblog for '.$listener->getName(),
            'system' =>             $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);

        return $this->render('listener/ndbweblog/ndbweblog.html.twig', $parameters);
    }
}
