<?php
namespace App\Controller\Web\Users;

use App\Entity\User as UserEntity;
use App\Form\Users\Users as Form;
use App\Form\Users\UserView as UserViewForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Users
 * @package App\Controller\Web
 */
class User extends Base
{
    const EDITABLE_FIELDS = [
        'access', 'active', 'email', 'name', 'username'
    ];

    /**
     * @Route(
     *     "/{_locale}/{system}/users/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="user"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param UserViewForm $userViewForm
     * @return RedirectResponse|Response
     */
    public function user(
        $_locale,
        $system,
        $id,
        Request $request,
        UserViewForm $userViewForm
    ) {
        if (!((int)$this->parameters['access'])) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'user?id=' . $id);
                return $this->redirectToRoute('logon', ['system' => $system]);
            }
            throw $this->createAccessDeniedException('You do not have access to this page');
        }
        $this->session->set('route', '');
        $operation = $id;
        if ($id === 'new') {
            $id = false;
        }
        $doReloadOpener = $this->session->get('reloadOpener') ?? false;
        $this->session->set('reloadOpener', false);
        $reloadOpener = false;
        if ((int) $id) {
            if (!$user = $this->getValidUser($id)) {
                return $this->redirectToRoute('users', ['system' => $system]);
            }
            $reloadOpener = true;
        } else {
            $user = new UserEntity();
        }

        $isAdmin = $this->parameters['isAdmin'];

        if (!$id && !$isAdmin) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $options = [
            'id' =>         $user->getId(),
        ];
        foreach (static::EDITABLE_FIELDS as $f) {
            $options[$f] = $user->{'get' . ucfirst($f)}();
        }
        $form = $userViewForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ((int)$id) {
                $user = $this->userRepository->find($id);
            } else {
                $user = new UserEntity();
            }
            foreach (static::EDITABLE_FIELDS as $f) {
                $user->{'set' . ucfirst($f)}($form_data[$f]);
            }
            if ($form_data['password'] ?? false) {
                $user->setPassword(password_hash($form_data['password'], PASSWORD_DEFAULT));
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $id = $user->getId();

            if ($form_data['_close']) {
                $js =
                    ($doReloadOpener ? "window.opener.document.getElementsByName('form')[0].submit();" : '')
                    . "window.close()";
                return new Response("<script>$js</script>", Response::HTTP_OK, [ 'content-type' => 'text/html' ]);
            }
            $this->session->set('reloadOpener', 1);
            return $this->redirectToRoute('admin/user', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'form' =>               $form->createView(),
            'u' =>                  $user,
            'mode' =>               !$id ? $this->i18n('Add New User') : sprintf($this->i18n('Edit %s User Profile'), $user->getUsername()),
            'doReloadOpener' =>     $doReloadOpener,
            'reloadOpener' =>       $reloadOpener,
            'system' =>             $system,
            'tabs' =>               $this->userRepository->getTabs($user)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('user/edit.html.twig', $parameters);
    }

}
