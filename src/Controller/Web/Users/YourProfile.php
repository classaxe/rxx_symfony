<?php
namespace App\Controller\Web\Users;

use App\Form\Users\UserProfile as UserProfileForm;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Users
 * @package App\Controller\Web
 */
class YourProfile extends Base
{
    const PROFILE_FIELDS = [
        'email', 'name'
    ];

    /**
     * @Route(
     *     "/{_locale}/{system}/profile",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="profile"
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
            $this->session->set('route', 'profile');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }

        $this->session->set('route', '');
        $this->session->set('lastMessage', '');
        $this->session->set('lastError', '');

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

            return $this->redirectToRoute('profile', [ 'system' => $system ]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'form' =>               $form->createView(),
            'u' =>                  $user,
            'mode' =>               'Your Profile',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('user/profile.html.twig', $parameters);
    }
}
