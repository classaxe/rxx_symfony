<?php
namespace App\Controller\Web\Admin;

use App\Entity\User as UserEntity;
use App\Form\Users\Users as Form;
use App\Form\Users\UserProfile as UserProfileForm;
use App\Form\Users\UserView as UserViewForm;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Users
 * @package App\Controller\Web
 */
class Users extends Base
{
    const defaultlimit =     -1;
    const defaultSorting =  'username';
    const defaultOrder =    'a';

    const EDITABLE_FIELDS = [
        'access', 'active', 'email', 'name', 'username'
    ];
    const PROFILE_FIELDS = [
        'email', 'name'
    ];


    /**
     * @Route(
     *     "/{_locale}/{system}/admin/profile",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/profile"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param UserProfileForm $userProfileForm
     * @return RedirectResponse|Response
     */
    public function profile(
        $_locale,
        $system,
        Request $request,
        UserProfileForm $userProfileForm
    ) {
        if ((int)$this->parameters['access'] === 0) {
            $this->session->set('route', 'admin/profile');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }

        $this->session->set('route', '');
        $id = $this->session->get('user_id');
        if (!$user = $this->getValidUser($id)) {
            throw $this->createAccessDeniedException('You do not have access to this page');
        }
        $options = [
            'id' =>         $id,
        ];
        foreach (static::PROFILE_FIELDS as $f) {
            $options[$f] = $user->{'get' . ucfirst($f)}();
        }
        $form = $userProfileForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            $user = $this->userRepository->find($id);
            foreach (static::PROFILE_FIELDS as $f) {
                $user->{'set' . ucfirst($f)}($form_data[$f]);
            }
            if ($form_data['password'] ?? false) {
                $user->setPassword(password_hash($form_data['password'], PASSWORD_DEFAULT));
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            if ($form_data['password'] ?? false) {
                $this->session->set(
                    'lastMessage',
                    $this->i18n("Your Password has ben updated.")
                );
            } else {
                $this->session->set(
                    'lastMessage',
                    $this->i18n("Your Profile has been saved.")
                );
            }

            return $this->redirectToRoute('admin/profile', [ 'system' => $system ]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'classic' =>            $this->systemRepository->getClassicUrl('profile'),
            'form' =>               $form->createView(),
            'u' =>                  $user,
            'mode' =>               'Your Profile',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('user/profile.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/admin/users/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/user"
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
        if (!((int)$this->parameters['access'] & UserEntity::MASTER)) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'admin/user?id=' . $id);
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
                return $this->redirectToRoute('admin/users', ['system' => $system]);
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
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('user/edit.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/admin/users",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/users"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function users(
        $_locale,
        $system,
        Request $request,
        Form $form
    ) {
        if (!((int)$this->parameters['access'] & UserEntity::MASTER)) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'admin/users');
                return $this->redirectToRoute('logon', ['system' => $system]);
            }
            throw $this->createAccessDeniedException('You do not have access to this page');
        }
        $this->session->set('route', '');
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $this->userRepository->getCount()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        $parameters = [
            'args' =>               $args,
            'classic' =>            $this->systemRepository->getClassicUrl('users'),
            'columns' =>            $this->userRepository->getColumns(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->i18n('of %s User Accounts'), $options['total']),
            'mode' =>               $this->i18n('User Accounts'),
            'records' =>            $this->userRepository->getRecords($args),
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'system' =>             $system
        ];
        return $this->render('users/index.html.twig', $this->getMergedParameters($parameters));
    }
}
