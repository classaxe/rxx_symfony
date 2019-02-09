<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use App\Form\Logon as LogonForm;
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
     *     "/{locale}/{system}/admin/logon",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logon"
     * )
     */
    public function logonController(
        $locale,
        $system,
        Request $request,
        LogonForm $form
    ) {
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
                return $this->redirectToRoute('logon', ['system' => $system]);
            } else {
                if (!$this->session->get('isAdmin', 0)) {
                    $this->session->set('isAdmin', 1);
                    $this->session->set('lastError', '');
                    return $this->redirectToRoute(
                        'logon',
                        [
                            'locale' => $locale,
                            'system' => $system
                        ]
                    );
                }
            }
        } else {
            $this->session->set('lastError', '');
        }
        $parameters = [
            'args' =>       $args,
            'form' =>       $form->createView(),
            'form_class' => 'logon',
            'locale' =>     $locale,
            'mode' =>       'Logon',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('logon/index.html.twig', $parameters);
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
        (new Dotenv())->load($this->kernel->getProjectDir().'/.env');
        $this->username = getenv('ADMIN_USER');
        $this->password = getenv('ADMIN_PASS');
        if (!$this->username || !$this->password) {
            return false;
        }
        return true;
    }
}
