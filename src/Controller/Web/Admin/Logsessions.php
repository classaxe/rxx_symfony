<?php
namespace App\Controller\Web\Admin;

use App\Entity\User as UserEntity;
use App\Form\LogSessions\LogSessions as Form;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Users
 * @package App\Controller\Web
 */
class Logsessions extends Base
{
    const defaultlimit = 100;
    const defaultSorting = 'timestamp';
    const defaultOrder = 'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/admin/logsessions",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/logsessions"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function logsessions(
        $_locale,
        $system,
        Request $request,
        Form $form
    )
    {
        if (!((int)$this->parameters['access'] & (UserEntity::MASTER | UserEntity::ADMIN))) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'admin/logsessions');
                return $this->redirectToRoute('logon', ['system' => $system]);
            }
            throw $this->createAccessDeniedException('You do not have access to this page');
        }

        $options = [
            'limit' => static::defaultlimit,
            'order' => static::defaultOrder,
            'page' => 0,
            'sort' => static::defaultSorting,
            'total' => $this->logsessionRepository->getLogsessionsCount()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' => static::defaultlimit,
            'order' => static::defaultOrder,
            'page' => 0,
            'sort' => static::defaultSorting,
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        $columns = $this->logsessionRepository->getColumns();
        $logSessions = $this->logsessionRepository->getLogsessions($args, $columns);
        $parameters = [
            'args' =>           $args,
            'classic' =>        $this->systemRepository->getClassicUrl('log_sessions'),
            'columns' =>        $columns,
            'form' =>           $form->createView(),
            '_locale' =>        $_locale,
            'matched' =>        'of ' . $options['total'] . ' log sessions.',
            'mode' =>           'Log Sessions',
            'logsessions' =>    $logSessions,
            'results' => [
                'limit' =>  isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>   isset($args['page']) ? $args['page'] : 0,
                'total' =>  $options['total']
            ],
            'system' =>         $system,
            'tabs' =>           [],
            'typeRepository' => $this->typeRepository
        ];
        return $this->render('log_sessions/index.html.twig', $this->getMergedParameters($parameters));
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/admin/logsessions/{logSessionId}/delete",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/logsession_delete"
     * )
     * @param $_locale
     * @param $system
     * @param $logSessionId
     * @return RedirectResponse
     */
    public function logSessionDelete(
        $_locale,
        $system,
        $logSessionId
    ) {
        if (!$this->parameters['isAdmin']) {
            return $this->redirectToRoute('listeners', ['_locale' => $_locale, 'system' => $system]);
        }

        $logSession = $this->logsessionRepository->find((int) $logSessionId);
        if (!$logSession) {
            return $this->redirectToRoute('admin/logsessions', ['_locale' => $_locale, 'system' => $system]);
        }

        $listenerId = $logSession->getListenerId();
        $listener = $this->listenerRepository->find($listenerId);

        $args = [
            'order' =>          'd',
            'sort' =>           'logDate',
            'logSessionId' =>   (int) $logSessionId
        ];

        $sortableColumns =  $this->listenerRepository->getColumns('logs');
        $logRecords =       $this->logRepository->getLogs($args, $sortableColumns);

        $em = $this->getDoctrine()->getManager();
        foreach($logRecords as $logRecord) {
            if ($log = $this->logRepository->find($logRecord['log_id'])) {
                $em->remove($log);
                $em->flush();
                $this->signalRepository->updateSignalStats($logRecord['id'], true, true);
            }
        }
        $em->remove($logSession);
        $em->flush();

        $this->listenerRepository->updateListenerStats($listenerId);

        $this->session->set(
            'lastMessage',
            sprintf(
                $this->i18n("Log Session has been deleted. All affected signals and stats for %s have been updated."),
                $listener->getName()
            )
        );
        return $this->redirectToRoute('admin/logsessions', ['_locale' => $_locale, 'system' => $system]);
    }

}