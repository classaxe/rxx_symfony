<?php
namespace App\Controller\Web\Donations;

use App\Entity\Donation as DonationEntity;
use App\Form\Donations\Donation as DonationForm;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Donation
 * @package App\Controller\Web\Donations
 */
class Donation extends Base
{
    const EDITABLE_FIELDS = [
        'donorID', 'amount', 'message'
    ];

    /**
     * @Route(
     *     "/{_locale}/{system}/donations/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="donation"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param DonationForm $donationForm
     * @return RedirectResponse|Response
     */
    public function donation(
        $_locale,
        $system,
        $id,
        Request $request,
        DonationForm $donationForm
    ) {
        if (!((int)$this->parameters['access'])) {
            if ((int)$this->parameters['access'] === 0) {
                $this->session->set('route', 'donation?id=' . $id);
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
            if (!$donation = $this->getValidDonation($id)) {
                return $this->redirectToRoute('donations', ['system' => $system]);
            }
            $reloadOpener = true;
        } else {
            $donation = new DonationEntity();
        }

        $isAdmin = $this->parameters['isAdmin'];

        if (!$id && !$isAdmin) {
            return $this->redirectToRoute('donors', ['system' => $system]);
        }
        $args = [
            'id' =>         $donation->getId(),
        ];
        foreach (static::EDITABLE_FIELDS as $f) {
            $args[$f] = $donation->{'get' . ucfirst($f)}();
        }
        $args['date'] = $donation->getDate() ? $donation->getDate()->format('Y-m-d') : (new DateTime())->format('Y-m-d');
        $form = $donationForm->buildForm(
            $this->createFormBuilder(),
            $args
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ((int)$id) {
                $donation = $this->donationRepository->find($id);
            } else {
                $donation = new DonationEntity();
            }
            foreach (static::EDITABLE_FIELDS as $f) {
                $donation->{'set' . ucfirst($f)}($form_data[$f]);
            }
            $donation->setDate(DateTime::createFromFormat('Y-m-d', $form_data['date']));
            $em = $this->getDoctrine()->getManager();
            $em->persist($donation);
            $em->flush();
            $id = $donation->getId();

            if ($form_data['_close']) {
                $js =
                    ($doReloadOpener ? "window.opener.document.getElementsByName('form')[0].submit();" : '')
                    . "window.close()";
                return new Response("<script>$js</script>", Response::HTTP_OK, [ 'content-type' => 'text/html' ]);
            }
            $this->session->set('reloadOpener', 1);
            return $this->redirectToRoute('donation', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'form' =>               $form->createView(),
            'd' =>                  $donation,
            'mode' =>               !$id ? $this->i18n('Add New Donation') : sprintf($this->i18n('Edit Donation %s'), $donation->getId()),
            'doReloadOpener' =>     $doReloadOpener,
            'reloadOpener' =>       $reloadOpener,
            'system' =>             $system,
            'tabs' =>               $this->donationRepository->getTabs($donation)
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('donation/edit.html.twig', $parameters);
    }

}
