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
class Collection extends Base
{
    const defaultlimit = 100;
    const defaultSorting = 'timestamp';
    const defaultOrder = 'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/logsessions",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="logsessions"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function Collection(
        $_locale,
        $system,
        Request $request,
        Form $form
    )
    {
        $args = [
            'limit' =>      static::defaultlimit,
            'order' =>      static::defaultOrder,
            'page' =>       0,
            'sort' =>       static::defaultSorting,
            'comment' =>    '',
            'location' =>   '',
            'type' =>       [],
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $columns = $this->logsessionRepository->getColumns();
        $logSessions = $this->logsessionRepository->getLogsessions($args, $columns);
        $total = $this->logsessionRepository->getLogsessionsCount($args);
        $parameters = [
            'args' =>           $args,
            'columns' =>        $columns,
            'form' =>           $form->createView(),
            '_locale' =>        $_locale,
            'matched' =>        'of ' . $total,
            'mode' =>           'Log Sessions',
            'logsessions' =>    $logSessions,
            'results' => [
                'limit' =>      $args['limit'],
                'page' =>       $args['page'],
                'total' =>      $total
            ],
            'system' =>         $system,
            'tabs' =>           [],
            'typeRepository' => $this->typeRepository
        ];
        return $this->render('log_sessions/index.html.twig', $this->getMergedParameters($parameters));
    }
}