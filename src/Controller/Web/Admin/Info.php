<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use App\Entity\User as UserEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Info extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/admin/info",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/info"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function controller(
        $_locale,
        $system
    ) {
        if (!((int)$this->parameters['access'] & UserEntity::MASTER)) {
            $this->session->set('route', 'admin/info');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }

        $this->session->set('route', '');
        $this->session->set('lastMessage', '');
        $this->session->set('lastError', '');

        $parameters = [
            '_locale' =>        $_locale,
            'mode' =>           'System Info',
            'info' =>           $this->systemRepository->getPhpInfo(),
            'mysql_version' =>  $this->systemRepository->getMySQLVersion(),
            'php_version' =>    phpversion(),
            'system' =>         $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/info/index.html.twig', $parameters);
    }

}
