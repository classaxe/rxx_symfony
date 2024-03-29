<?php
namespace App\Controller\Web\Users;

use DateTime;
use App\Controller\Web\Base;
use App\Entity\User;
use App\Form\Logon as LogonForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Users
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
        $disableLogon = getenv('DISABLE_LOGON') ?? false;
        $form = $form->buildForm($this->createFormBuilder(), ['system' => $system, 'disableLogon' => $disableLogon]);
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
            $this->session->set('access', $r->getAccess());
            $this->session->set('user_id', $r->getId());
            $this->session->set('user_name', $r->getName());
            $this->session->set('user_email', $r->getEmail());
            $this->session->set('lastError', '');
            if ($r->getAccess() | User::ADMIN) {
                $this->session->set('isAdmin', 1);
                $this->session->set('isMember', 0);
                $this->session->set('lastMessage', 'You have logged on as an Administrator.');
            } else {
                $this->session->set('isAdmin', 0);
                $this->session->set('isMember', 1);
                $this->session->set('lastMessage', 'You have logged on as a Member.');
            }
            $r->setLogonCount($r->getLogonCount() +1);
            $r->setLogonLatest(new DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $parameters = [
                '_locale' => $_locale,
                'system' => $system
            ];
            if ($this->session->get('route')) {
                $parts = explode('?', $this->session->get('route'));
                if (isset($parts[1])) {
                    $arg_arr = explode('&', $parts[1]);
                    foreach ($arg_arr as $arg) {
                        $arg_pair = explode('=', $arg);
                        $parameters[$arg_pair[0]] = $arg_pair[1] ?? '';
                    }
                }
                return $this->redirectToRoute($parts[0], $parameters);
            }
            return $this->redirectToRoute('logon', $parameters);
        } else {
            $this->session->set('lastError', '');
            $this->session->set('lastMessage', '');
        }
        $parameters = [
            'args' =>       $args,
            'disableLogon' =>   $disableLogon,
            'form' =>       $form->createView(),
            'form_class' => 'logon',
            '_locale' =>    $_locale,
            'mode' =>       'Logon',
            'system' =>     $system,
            'username' =>   $this->session->get('user_name')
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('admin/logon/index.html.twig', $parameters);
    }
}
