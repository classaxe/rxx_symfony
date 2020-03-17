<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use App\Form\Logon as LogonForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Logon extends Base
{

    private $username = '';
    private $password = '';

    /**
     * @Route(
     *     "/{_locale}/{system}/admin/logon",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logon"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param LogonForm $form
     * @return RedirectResponse|Response
     */
    public function logonController($_locale, $system, Request $request, LogonForm $form)
    {
        if (!$this->getConfig()) {
            return $this->configError();
        }

        $form = $form->buildForm($this->createFormBuilder(), ['system' => $system]);
        $form->handleRequest($request);
        $args = [
            'user' =>       '',
            'password' =>   ''
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            if ($args['user'] !== $this->username || $args['password'] !== $this->password) {
                $this->session->set('lastError', 'Incorrect Username and / or Password.');
                $parameters = [
                    '_locale' => $_locale,
                    'system' => $system
                ];

                return $this->redirectToRoute('logon', $parameters);
            }
            if (!$this->session->get('isAdmin', 0)) {
                $this->session->set('isAdmin', 1);
                $this->session->set('lastError', '');
                $parameters = [
                    '_locale' => $_locale,
                    'system' => $system
                ];

                return $this->redirectToRoute('logon', $parameters);
            }
        } else {
            $this->session->set('lastError', '');
        }
        $parameters = [
            'args' =>       $args,
            'classic' =>    $this->systemRepository->getClassicUrl($system, 'logon'),
            'form' =>       $form->createView(),
            'form_class' => 'logon',
            '_locale' =>    $_locale,
            'mode' =>       'Logon',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/logon/index.html.twig', $parameters);
    }

    private function configError()
    {
        return $this->rxx::error(
            'ADMIN_USER and ADMIN_PASS environment variables must be defined in server or a .env file.'
        );
    }

    private function getConfig()
    {
        if (!getenv('ADMIN_USER') || !getenv('ADMIN_PASS')) {
            if (!class_exists(Dotenv::class)) {
                return false;
            }
        }
        (new Dotenv(true))->load($this->kernel->getProjectDir().'/.env');
        $this->username = getenv('ADMIN_USER');
        $this->password = getenv('ADMIN_PASS');
        if (!$this->username || !$this->password) {
            return false;
        }
        return true;
    }
}
