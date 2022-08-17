<?php
namespace App\Controller\Web\Users;

use App\Form\Listeners\ListenerLogs as Form;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class UserLogsessions extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'timestamp';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/users/{id}/logsessions",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="user_logsessions"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function logSessions(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form
    ) {
        if ((int)$this->parameters['access'] === 0) {
            $this->session->set('route', 'user_logsessions');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }
        if (!$user = $this->getValidUser($id)) {
            return $this->redirectToRoute('admin/users', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $user->getCountLogsession()
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
        $args['administratorId'] = $id;
        $columns = $this->userRepository->getColumns('logsessions');
        $logSessions = $this->logsessionRepository->getLogsessions($args, $columns);

        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            'of '.$options['total']. ' log sessions.',
            'mode' =>               'Log Sessions | Uploaded by ' . $user->getName(),
            'logsessions' =>        $logSessions,
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'system' =>             $system,
            'tabs' =>               $this->userRepository->getTabs($user),
            'typeRepository' =>     $this->typeRepository
        ];
        return $this->render('user/logsessions.html.twig', $this->getMergedParameters($parameters));
    }
}
