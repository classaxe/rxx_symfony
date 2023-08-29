<?php
namespace App\Controller\Web\Donors;

use App\Entity\Donor as DonorEntity;
use App\Form\Donors\Donor as DonorForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Donor
 * @package App\Controller\Web
 */
class Donor extends Base
{
    const EDITABLE_FIELDS = [
        'name', 'display', 'email', 'callsign', 'anonymous', 'itu', 'sp', 'notes'
    ];

    /**
     * @Route(
     *     "/{_locale}/{system}/donors/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="donor"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param DonorForm $donorForm
     * @return RedirectResponse|Response
     */
    public function donor(
        $_locale,
        $system,
        $id,
        Request $request,
        DonorForm $donorForm
    ) {
        if (!((int)$this->parameters['access'])) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'donor?id=' . $id);
                return $this->redirectToRoute('logon', ['system' => $system]);
            }
            throw $this->createAccessDeniedException('You do not have access to this page');
        }
        $this->session->set('route', '');
        $operation = $id;
        if ($id === 'new') {
            $id = false;
        }
        $doReloadOpener = $this->session->get('reloadOpener') ?? false;
        $this->session->set('reloadOpener', false);
        $reloadOpener = false;
        if ((int) $id) {
            if (!$donor = $this->getValidDonor($id)) {
                return $this->redirectToRoute('donors', ['system' => $system]);
            }
            $reloadOpener = true;
        } else {
            $donor = new DonorEntity();
        }

        $isAdmin = $this->parameters['isAdmin'];

        if (!$id && !$isAdmin) {
            return $this->redirectToRoute('donors', ['system' => $system]);
        }
        $options = [
            'id' =>         $donor->getId(),
        ];
        foreach (static::EDITABLE_FIELDS as $f) {
            $options[$f] = $donor->{'get' . ucfirst($f)}();
        }
        $form = $donorForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ((int)$id) {
                $donor = $this->donorRepository->find($id);
            } else {
                $donor = new DonorEntity();
            }
            foreach (static::EDITABLE_FIELDS as $f) {
                $donor->{'set' . ucfirst($f)}($form_data[$f]);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($donor);
            $em->flush();
            $id = $donor->getId();

            if ($form_data['_close']) {
                $js =
                    ($doReloadOpener ? "window.opener.document.getElementsByName('form')[0].submit();" : '')
                    . "window.close()";
                return new Response("<script>$js</script>", Response::HTTP_OK, [ 'content-type' => 'text/html' ]);
            }
            $this->session->set('reloadOpener', 1);
            return $this->redirectToRoute('donor', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'form' =>               $form->createView(),
            'd' =>                  $donor,
            'mode' =>               !$id ? $this->i18n('Add New Donor') : sprintf($this->i18n('Edit %s Donor Profile'), $donor->getName()),
            'doReloadOpener' =>     $doReloadOpener,
            'reloadOpener' =>       $reloadOpener,
            'system' =>             $system,
            'tabs' =>               $this->donorRepository->getTabs($donor)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('donor/edit.html.twig', $parameters);
    }

}
