<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Logoff extends Base
{

    /**
     * @Route(
     *     "/{_locale}/{system}/admin/logoff",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logoff"
     * )
     */
    public function logoffController(
        $_locale,
        $system
    ) {
        $this->session->set('isAdmin', 0);
        return $this->redirectToRoute(
            'logon',
            [
                '_locale' => $_locale,
                'system' => $system
            ]
        );
    }
}
