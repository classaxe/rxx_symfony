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
     *     "/{system}/admin/logoff",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="logoff"
     * )
     */
    public function logoffController($system)
    {
        $this->session->set('isAdmin', 0);
        return $this->redirectToRoute('logon', ['system' => $system]);
    }
}
