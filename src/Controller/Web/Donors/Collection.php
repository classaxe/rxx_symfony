<?php
namespace App\Controller\Web\Donors;

use App\Entity\User as UserEntity;
use App\Form\Donors\Donors as Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Web\Base as WebBase;
use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Donors
 * @package App\Controller\Web
 */
class Collection extends WebBase
{
    const defaultlimit =    100;
    const defaultSorting =  'name';
    const defaultOrder =    'a';

    /**
     * @Route(
     *     "/{_locale}/{system}/donors",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="donors"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param Form $form
     * @return RedirectResponse|Response
     */
    public function index(
        $_locale,
        $system,
        Request $request,
        Form $form
    ) {
        if (!((int)$this->parameters['access'] & UserEntity::MASTER)) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'donors');
                return $this->redirectToRoute('logon', ['system' => $system]);
            }
            throw $this->createAccessDeniedException('You do not have access to this page');
        }

        $this->session->set('route', '');
        $this->session->set('lastMessage', '');
        $this->session->set('lastError', '');

        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $this->donorRepository->getCount()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $parameters = [
            'args' =>               $args,
            'columns' =>            $this->donorRepository->getColumns(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->i18n('of %s Donor Profiles'), $args['total']),
            'mode' =>               $this->i18n('Donor Profiles'),
            'records' =>            $this->donorRepository->getRecords($args),
            'results' => [
                'limit' =>          $args['limit'],
                'page' =>           $args['page'],
                'total' =>          $args['total']
            ],
            'stats' =>              $this->donorRepository->getStats(),
            'system' =>             $system
        ];

        return $this->render('donors/index.html.twig', $this->getMergedParameters($parameters));
    }
}
