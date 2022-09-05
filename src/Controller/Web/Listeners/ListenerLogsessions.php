<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerLogSessions as Form;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerLogsessions extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'timestamp';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logsessions",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logsessions"
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
        return $this->displayLogSessions($_locale, $system, $id, $request, $form, false);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/remotelogsessions",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_remote_logsessions"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function remoteLogSessions(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form
    ) {
        return $this->displayLogSessions($_locale, $system, $id, $request, $form, true);
    }

    private function displayLogSessions($_locale, $system, $id, $request, $form, $isRemote) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $isRemote ? $listener->getCountRemoteLogsessions() : $listener->getCountLogsessions()
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
        if ($isRemote) {
            $args['operatorId'] =   $id;
            $columns =              'remotelogsessions';
            $matchedSuffix =        'of %s remote log sessions.';
            $mode =                 'Remote Log Sessions | %s';
            $template =             'listener/remotelogsessions.html.twig';
        } else {
            $args['listenerId'] =   $id;
            $columns =              'logsessions';
            $matchedSuffix =        'of %s log sessions.';
            $mode =                 'Log Sessions | %s';
            $template =             'listener/logsessions.html.twig';
        }
        $columns =      $this->listenerRepository->getColumns($columns);
        $logSessions =  $this->logsessionRepository->getLogsessions($args, $columns);

        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'matched' =>            sprintf($matchedSuffix, $options['total']),
            'mode' =>               sprintf($mode, $listener->getFormattedNameAndLocation()),
            'logsessions' =>        $logSessions,
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin),
            'typeRepository' =>     $this->typeRepository
        ];
        return $this->render($template, $this->getMergedParameters($parameters));
    }

}
