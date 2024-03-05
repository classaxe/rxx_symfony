<?php
namespace App\Controller\Web\Donations;

use App\Entity\User as UserEntity;
use App\Form\Donations\Donations as Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Web\Base as WebBase;
use App\Utils\Rxx;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Donations
 * @package App\Controller\Web\Donations
 */
class Collection extends WebBase
{
    const defaultlimit =     100;
    const defaultSorting =  'id';
    const defaultOrder =    'd';

    /**
     * @Route(
     *     "/{_locale}/{system}/donations",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="donations"
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
                $this->session->set('route', 'donations');
                return $this->redirectToRoute('logon', ['system' => $system]);
            }
            throw $this->createAccessDeniedException('You do not have access to this page');
        }
        $this->session->set('route', '');
        $args = [
            'limit' =>          static::defaultlimit,
            'order' =>          static::defaultOrder,
            'page' =>           0,
            'sort' =>           static::defaultSorting,
            'total' =>          $this->donationRepository->getCount()
        ];
        $form = $form->buildForm($this->createFormBuilder(), $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData() + $args;
        }
        $parameters = [
            'args' =>               $args,
            'columns' =>            $this->donationRepository->getColumns(),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'matched' =>            sprintf($this->i18n('of %s Donations'), $args['total']),
            'mode' =>               $this->i18n('Donations'),
            'records' =>            $this->donationRepository->getRecords($args),
            'results' => [
                'limit' =>          $args['limit'],
                'page' =>           $args['page'],
                'total' =>          $args['total']
            ],
            'system' =>             $system
        ];
        return $this->render('donations/index.html.twig', $this->getMergedParameters($parameters));
    }
}
