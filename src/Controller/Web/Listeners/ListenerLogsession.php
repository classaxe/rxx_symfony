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
class ListenerLogsession extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logsessions/{logSessionId}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logsession"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param $logSessionId
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function logSession(
        $_locale,
        $system,
        $id,
        $logSessionId,
        Request $request,
        Form $form
    ) {
        if (!$listener = $this->getValidReportingListener($id)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $args = [
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'order' =>          'd',
            'sort' =>           'logDate',
            'logSessionId' =>   $logSessionId
        ];
        $logs =           $this->logRepository->getLogs($args, $this->listenerRepository->getColumns('logs'));
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $this->listenerRepository->getColumns('logs'),
            '_locale' =>            $_locale,
            'isMultiOperator' =>    ($listener->getMultiOperator() === 'Y'),
            'logs' =>               $logs,
            'system' =>             $system,
            'typeRepository' =>     $this->typeRepository
        ];
        return $this->render('listener/logsessionlogs.html.twig', $this->getMergedParameters($parameters));
    }
}
