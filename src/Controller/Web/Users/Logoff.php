<?php
namespace App\Controller\Web\Users;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Users
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
        $this->session->set('access', '');
        $this->session->set('isAdmin', 0);
        $this->session->set('isMember', 0);
        $this->session->set('lastMessage', 'You have logged off.');
        $this->session->set('route', '');
        $this->session->set('user_id', '');
        $this->session->set('user_name', '');
        $this->session->set('user_email', '');

        $parameters = [
            '_locale' => $_locale,
            'system' => $system
        ];

        return $this->redirectToRoute('logon', $parameters);
    }
}
