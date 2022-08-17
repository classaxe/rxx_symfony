<?php
namespace App\Controller\Web\Listeners;

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
class ListenerRemoteLogsessions extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'timestamp';
    const defaultOrder =    'd';

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
    public function index(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form
    ) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $isAdmin = $this->parameters['isAdmin'];
        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $listener->getCountRemoteLogsessions()
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
        $args['operatorId'] = $id;
        $columns = $this->listenerRepository->getColumns('remotelogsessions');
        $logSessions = $this->logsessionRepository->getLogsessions($args, $columns);

        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'matched' =>            'of '.$options['total']. ' log sessions.',
            'mode' =>               'Remote Log Sessions | '.$listener->getFormattedNameAndLocation(),
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
        return $this->render('listener/remotelogsessions.html.twig', $this->getMergedParameters($parameters));
    }
}
