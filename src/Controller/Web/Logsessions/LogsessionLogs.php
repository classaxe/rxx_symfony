<?php
namespace App\Controller\Web\Logsessions;

use App\Controller\Web\Base;
use App\Form\Listeners\ListenerLogs as Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class LogsessionLogs
 * @package App\Controller\Web
 */
class LogsessionLogs extends Base
{
    const defaultlimit =     100;
    const defaultSorting =  'logDate';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions/{id}/logs",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logsession_logs"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form
    ) {
        if (!(int) $id || !$logsession = $this->logsessionRepository->find($id)) {
            return $this->redirectToRoute('logsession', ['system' => $system]);
        }
        $args = [
            'logsessionID' =>   $id,
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $logsession->getLogs()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $columns = $this->listenerRepository->getColumns('logs');
        $logs = $this->logRepository->getLogs($args, $columns);

        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $columns,
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            'of ' . $args['total'] . ' log records.',
            'mode' =>               "Logs | Log Session $id",
            'logs' =>               $logs,
            'results' => [
                'limit' =>          $args['limit'],
                'page' =>           $args['page'],
                'total' =>          $args['total']
            ],
            'system' =>             $system,
            'tabs' =>               $this->logsessionRepository->getTabs($logsession, $this->parameters['isAdmin']),
            'typeRepository' =>     $this->typeRepository
        ];
        return $this->render('logsession/logs.html.twig', $this->getMergedParameters($parameters));
    }
}
