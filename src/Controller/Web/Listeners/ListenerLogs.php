<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerLogs as Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerLogs extends Base
{
    const defaultlimit =     1000;
    const defaultSorting =  'logDate';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logs",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logs"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function logs(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form
    ) {
        return $this->displayLogs($_locale, $system, $id, $request, $form, false);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/remotelogs",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_remote_logs"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function remoteLogs(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form
    ) {
        return $this->displayLogs($_locale, $system, $id, $request, $form, true);
    }

    private function displayLogs($_locale, $system, $id, $request, $form, $isRemote) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $isRemote ? $listener->getCountRemoteLogs() : $listener->getCountLogs()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $options);
        $form->handleRequest($request);
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting
        ];
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }
        if ($isRemote) {
            $args['operatorId'] =   $id;
            $columns =              'remotelogs';
            $matchedSuffix =        'of %s remote log records.';
            $mode =                 'Remote Logs | %s';
            $template =             'listener/remotelogs.html.twig';
        } else {
            $args['listenerId'] =   $id;
            $columns =              'logs';
            $matchedSuffix =        'of %s log records.';
            $mode =                 'Logs | %s';
            $template =             'listener/logs.html.twig';
        }

        $columns = $this->listenerRepository->getColumns($columns);
        $logs = $this->logRepository->getLogs($args, $columns);

        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'matched' =>            sprintf($matchedSuffix, $options['total']),
            'mode' =>               sprintf($mode, $listener->getFormattedNameAndLocation()),
            'logs' =>               $logs,
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
