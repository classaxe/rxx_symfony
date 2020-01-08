<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerLogs as Form;
use App\Repository\ListenerRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web
 */
class ListenerLogs extends Base
{
    const defaultlimit =     20;
    const defaultSorting =  'logDate';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/logs",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_logs"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param Form $form
     * @param ListenerRepository $listenerRepository
     * @param TypeRepository $typeRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function logsController(
        $_locale,
        $system,
        $id,
        Request $request,
        Form $form,
        ListenerRepository $listenerRepository,
        TypeRepository $typeRepository
    ) {
        if (!$listener = $this->getValidReportingListener($id, $listenerRepository)) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }

        $options = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $listener->getCountLogs()
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
        $parameters = [
            'args' =>               $args,
            'id' =>                 $id,
            'columns' =>            $listenerRepository->getLogsColumns(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            'of '.$options['total']. ' log records.',
            'mode' =>               'Logs for '.$listener->getFormattedNameAndLocation(),
            'logs' =>               $listenerRepository->getLogsForListener($id, $args),
            'results' => [
                'limit' =>              isset($args['limit']) ? $args['limit'] : static::defaultlimit,
                'page' =>               isset($args['page']) ? $args['page'] : 0,
                'total' =>              $options['total']
            ],
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
            'typeRepository' =>     $typeRepository
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/logs.html.twig', $parameters);
    }
}
