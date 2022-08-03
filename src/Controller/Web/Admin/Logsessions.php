<?php
namespace App\Controller\Web\Admin;

use App\Entity\User as UserEntity;
use App\Form\LogSessions\LogSessions as Form;
use App\Form\LogSessions\LogSession as LogSessionViewForm;


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
     *     "/{_locale}/{system}/admin/logsessions/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="admin/logsession"
     * )
     * @param $_locale
     * @param $system
     * @param $logSessionId
     * @return RedirectResponse
     */
    public function logSession(
        $_locale,
        $system,
        $id,
        Request $request,
        LogSessionViewForm $logSessionViewForm
    ) {
        if (!((int)$this->parameters['access'] & (UserEntity::MASTER | UserEntity::ADMIN))) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'admin/logsessions');
                return $this->redirectToRoute('logon', ['system' => $system]);
            }
            throw $this->createAccessDeniedException('You do not have access to this page');
        }
        if (!$logsession = $this->logsessionRepository->find($id)) {
            return $this->redirectToRoute('admin/logsession', ['system' => $system]);
        }
        $doReload = $request->query->get('reload') ?? false;

        $options = [
            'id' =>         $logsession->getId(),
            'comment' =>    $logsession->getComment(),
            'listenerId' => $logsession->getListenerId(),
            'operatorId' => $logsession->getOperatorId(),
        ];
//        print "<pre>" . print_r($options, true) . "</pre>";
        $form = $logSessionViewForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data =             $form->getData();
            $oldListenerId =    $options['listenerId'];
            $oldOperatorId =    $options['operatorId'];
            $newOperatorId =    $data['operatorId'];
            $newListenerId =    $data['listenerId'];
            $comment =          $data['comment'];
            $logsession
                ->setListenerId($data['listenerId'])
                ->setOperatorId($data['operatorId'] ? (int)$data['operatorId'] : null)
                ->setComment($comment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($logsession);
            $em->flush();

            $args = [
                'order' =>          'd',
                'sort' =>           'logDate',
                'logSessionId' =>   (int) $id
            ];


            $em = $this->getDoctrine()->getManager();
            if ($oldListenerId !== $newListenerId) {
                // Fix log locations and operators
                $sortableColumns =  $this->listenerRepository->getColumns('logs');
                $logRecords =       $this->logRepository->getLogs($args, $sortableColumns);
                foreach ($logRecords as $logRecord) {
                    $log = $this->logRepository->find($logRecord['log_id']);
                    $log->setListenerId($data['listenerId'])
                        ->setOperatorId($data['operatorId'] ? (int)$data['operatorId'] : null);
                    $em->flush();
                }

                // Fix DX and Daytime designations
                $this->logRepository->updateDx(false, false, $id);
                $this->logRepository->updateDaytime(false, false, $id);

                // Fix signal stats
                $logRecords =       $this->logRepository->getLogs($args, $sortableColumns);
                foreach ($logRecords as $logRecord) {
                    $this->logRepository->find($logRecord['log_id']);
                    $this->signalRepository->updateSignalStats($logRecord['id'], true, true);
                }

                // Fix old and new listener stats
                $this->listenerRepository->updateListenerStats($oldListenerId);
                $this->listenerRepository->updateListenerStats($newListenerId);
            }
            if ((int)$oldOperatorId !== (int)$newOperatorId) {
                // Update old and new operator stats
                if ((int)$oldOperatorId) {
                    $this->listenerRepository->updateListenerStats($oldOperatorId);
                }
                if ((int)$newOperatorId) {
                    $this->listenerRepository->updateListenerStats($newOperatorId);
                }
            }

            if ($data['_close']) {
                $js =
                    ($doReload ?
                        "window.opener.document.getElementsByName('form')[0].submit();" : ''
                    )
                    . "window.close()";
                return new Response(
                    "<script>$js</script>",
                    Response::HTTP_OK,
                    [ 'content-type' => 'text/html' ]
                );
            }

            return $this->redirectToRoute('admin/logsession', ['system' => $system, 'id' => $id, 'reload' =>1]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'doReload' =>           $doReload,
            'form' =>               $form->createView(),
            'l' =>                  $logsession,
            'mode' =>               'Edit Log Session',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('logsession/edit.html.twig', $parameters);    }

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
        $operatorId = $logSession->getOperatorId();

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
        if ($operatorId) {
            $this->listenerRepository->updateListenerStats($operatorId);
        }

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