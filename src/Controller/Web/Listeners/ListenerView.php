<?php
namespace App\Controller\Web\Listeners;

use App\Entity\Listener as ListenerEntity;
use App\Form\Listeners\ListenerView as ListenerViewForm;

use App\Utils\Rxx;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;


/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class ListenerView extends Base
{
    const EDITABLE_FIELDS = [
        'active',
        'callsign',
        'email',
        'equipment',
        'itu',
        'mapX',
        'mapY',
        'multiOperator',
        'name',
        'notes',
        'primaryQth',
        'qth',
        'sp',
        'timezone',
        'website',
        'wwsuEnable',
        'wwsuKey',
        'wwsuPermCycle',
        'wwsuPermOffsets',
    ];

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param ListenerViewForm $listenerViewForm
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        ListenerViewForm $listenerViewForm
    ) {
        if ($id === 'new') {
            $id = false;
        }
        if ((int) $id) {
            if (!$listener = $this->getValidListener($id)) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        } else {
            $listener = new ListenerEntity();
        }

        $isAdmin = $this->parameters['isAdmin'];

        if (!$id && !$isAdmin) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $options = [
            'isAdmin' =>    $isAdmin,
            'id' =>         $listener->getId(),
            'gsq' =>        $listener->getGsq(),
            'lat' =>        $listener->getLat(),
            'lon' =>        $listener->getLon(),
        ];
        foreach (static::EDITABLE_FIELDS as $f) {
            $options[$f] = $listener->{'get' . ucfirst($f)}();
        }
        $form = $listenerViewForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ((int)$id) {
                $listener = $this->listenerRepository->find($id);
            } else {
                $listener = new ListenerEntity();
            }
            if ($form_data['gsq'] && $a = $this->rxx::convertGsqToDegrees($form_data['gsq'])) {
                $lat =  $a["lat"];
                $lon =  $a["lon"];
                $GSQ =  $a["GSQ"];
            } else {
                $GSQ =  '';
                $lat =  0;
                $lon =  0;
            }
            $region = $this->countryRepository->getRegionForCountry($form_data['itu']);
            $listener
                ->setGsq($GSQ)
                ->setLat($lat)
                ->setLon($lon)
                ->setRegion($region);
            foreach (static::EDITABLE_FIELDS as $f) {
                $listener->{'set' . ucfirst($f)}($form_data[$f]);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($listener);
            $em->flush();
            $id = $listener->getId();

            if ($form_data['_close']) {
                return new Response(
                    '<script>window.close();</script>',
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                );
            }

            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            '_locale' =>            $_locale,
            'id' =>                 $id,
            'form' =>               $form->createView(),
            'l' =>                  $listener,
            'mode' =>               ($isAdmin && !$id ? 'Add Listener' : 'Profile | ' . $listener->getFormattedNameAndLocation()),
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/profile.html.twig', $parameters);
    }
}
