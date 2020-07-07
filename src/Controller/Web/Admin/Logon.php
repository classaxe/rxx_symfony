<?php
namespace App\Controller\Web\Admin;

use App\Controller\Web\Base;
use App\Form\Logon as LogonForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class Logon extends Base
{

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
        $form = $form->buildForm($this->createFormBuilder(), ['system' => $system]);
        $form->handleRequest($request);
        $args = [
            'user' =>       '',
            'password' =>   ''
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $result = $this->userRepository->logon($data['user'], $data['password']);
            if ($result['error']) {
                $this->session->set('lastError', $result['error']);
                $this->session->set('lastMessage', '');
                $parameters = [
                    '_locale' => $_locale,
                    'system' => $system
                ];
                return $this->redirectToRoute('logon', $parameters);
            }
            $r = $result['record'];
            $this->session->set('user_name', $r->getName());
            $this->session->set('user_email', $r->getEmail());
            $this->session->set('lastError', '');
            if ($r->getAdmin() === 1) {
                $this->session->set('isAdmin', 1);
                $this->session->set('isMember', 0);
                $this->session->set('lastMessage', 'You have logged on as an Administrator.');
            } else {
                $this->session->set('isAdmin', 0);
                $this->session->set('isMember', 1);
                $this->session->set('lastMessage', 'You have logged on as a Member.');
            }
            $parameters = [
                '_locale' => $_locale,
                'system' => $system
            ];
            if ($this->session->get('route')) {
                return $this->redirectToRoute($this->session->get('route'), $parameters);
            }
            return $this->redirectToRoute('logon', $parameters);
        } else {
            $this->session->set('lastError', '');
            $this->session->set('lastMessage', '');
        }
        $parameters = [
            'args' =>       $args,
            'classic' =>    $this->systemRepository->getClassicUrl('logon'),
            'form' =>       $form->createView(),
            'form_class' => 'logon',
            '_locale' =>    $_locale,
            'mode' =>       'Logon',
            'system' =>     $system,
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/logon/index.html.twig', $parameters);
    }
}
