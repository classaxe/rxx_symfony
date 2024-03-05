<?php
namespace App\Controller\Web\Users;

use App\Entity\User as UserEntity;
use App\Form\Users\Users as Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Users
 * @package App\Controller\Web
 */
class Collection extends Base
{
    const defaultlimit =     -1;
    const defaultSorting =  'username';
    const defaultOrder =    'a';

    /**
     * @Route(
     *     "/{_locale}/{system}/users",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="users"
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
            $this->session->set('route', 'admin/info');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }
        $this->session->set('route', '');
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $this->userRepository->getCount()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $parameters = [
            'args' =>               $args,
            'columns' =>            $this->userRepository->getColumns('users'),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->i18n('of %s User Accounts'), $args['total']),
            'mode' =>               $this->i18n('User Accounts'),
            'records' =>            $this->userRepository->getRecords($args),
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $args['total']
            ],
            'system' =>             $system
        ];
        return $this->render('users/index.html.twig', $this->getMergedParameters($parameters));
    }
}
