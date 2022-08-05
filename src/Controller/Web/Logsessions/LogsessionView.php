<?php
namespace App\Controller\Web\Logsessions;

use App\Controller\Web\Base;
use App\Entity\User as UserEntity;
use App\Form\LogSessions\LogSessions as Form;
use App\Form\LogSessions\LogSession as LogSessionViewForm;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Collection
 * @package App\Controller\Web\Logsessions
 */
class LogsessionView extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logsession"
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
        if (!$logsession = $this->logsessionRepository->find($id)) {
            return $this->redirectToRoute('logsession', ['system' => $system]);
        }
        $doReload = $request->query->get('reload') ?? false;
        $isAdmin = $this->parameters['isAdmin'];

        $options = [
            'id' =>         $logsession->getId(),
            'isAdmin' =>    $isAdmin,
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
            return $this->redirectToRoute('logsession', ['system' => $system, 'id' => $id, 'reload' =>1]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'doReload' =>           $doReload,
            'form' =>               $form->createView(),
            'l' =>                  $logsession,
            'mode' =>               'Log Session',
            'system' =>             $system
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('logsession/edit.html.twig', $parameters);
    }

}