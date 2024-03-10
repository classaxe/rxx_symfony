<?php
namespace App\Controller\Web\Cle;

use App\Controller\Web\Base;
use App\Entity\User as UserEntity;
use App\Form\Cle\ClePlanner as Form;

use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Users
 * @package App\Controller\Web
 */
class ClePlanner extends Base
{

    const defaultlimit =     50;
//    const defaultSorting =  'khz';
    const defaultSorting =  'username';
    const defaultOrder =    'a';

    /**
     * @Route(
     *     "/{_locale}/{system}/cle/planner",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="clePlanner"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function planner(
        $_locale,
        $system,
        Request $request,
        Form $form,
        TypeRepository $typeRepository
    ) {
        if (!((int)$this->parameters['access'] & UserEntity::CLE)) {
            $this->session->set('route', 'clePlanner');
            return $this->redirectToRoute('logon', ['system' => $system]);
        }
        $this->typeRepository = $typeRepository;
        $this->session->set('route', '');
        $this->session->set('lastMessage', '');
        $this->session->set('lastError', '');

        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          static::defaultlimit,

            'channels' =>       '',
            'date_1' =>         null,
            'date_2' =>         null,
            'khz_1' =>          '',
            'khz_2' =>          '',
            'status' =>         [1],
            'type' =>           ['NDB']
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
            $args['total'] = $this->signalRepository->getClePlannerCount($args);
        }
        $args['signalTypes'] = $this->typeRepository->getSignalTypesSearched($args['type']);

        $parameters = [
            'args' =>               $args,
            'columns' =>            $this->cleRepository->getColumns('planner'),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->i18n('of %s Signals'), $args['total']),
            'mode' =>               $this->i18n('CLE Planner'),
            'records' =>            $this->signalRepository->getClePlanner($args),
            'results' => [
                'limit' =>          $args['limit'],
                'page' =>           $args['page'],
                'total' =>          $args['total']
            ],
            'stats' =>              $this->signalRepository->getClePlannerStats($args),
            'system' =>             $system,
            'typeRepository' =>     $typeRepository
        ];
        return $this->render('cle_planner/index.html.twig', $this->getMergedParameters($parameters));
    }
}
