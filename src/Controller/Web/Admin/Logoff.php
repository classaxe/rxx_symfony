<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logoff"
     * )
     * @param $_locale
     * @param $system
     * @return RedirectResponse
     */
    public function logoffController($_locale, $system)
    {
        $this->session->set('isAdmin', 0);
        $this->session->set('lastMessage', 'You have now logged off.');

        $parameters = [
            '_locale' => $_locale,
            'system' => $system
        ];

        return $this->redirectToRoute('logon', $parameters);
    }
}
