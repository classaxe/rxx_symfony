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
class Help extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/ndbweblog/help",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"id"=""},
     *     name="listener_ndbweblog_help"
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
        return $this->render('listener/ndbweblog/help.html.twig');
    }
}
