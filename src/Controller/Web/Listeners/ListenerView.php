<?php
namespace App\Controller\Web\Listeners;

use App\Entity\Listener as ListenerEntity;
use App\Form\Listeners\ListenerView as ListenerViewForm;
use App\Repository\CountryRepository;
use App\Repository\ListenerRepository;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Rxx;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class ListenerView extends Base
{
    const EDITABLE_FIELDS = [
        'callsign', 'email', 'equipment', 'itu', 'mapX', 'mapY', 'name', 'notes', 'primaryQth', 'qth', 'sp', 'timezone', 'website'
    ];

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener"
     * )
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        CountryRepository $countryRepository,
        ListenerViewForm $listenerViewForm,
        ListenerRepository $listenerRepository
    ) {
        if ($id === 'new') {
            $id = false;
        }
        if ((int) $id) {
            if (!$listener = $this->getValidListener($id, $listenerRepository)) {
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
            'lon' =>        $listener->getLon()
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
                $listener = $listenerRepository->find($id);
            } else {
                $listener = new ListenerEntity();
                $listener
                    ->setLogLatest(Rxx::getUtcDateTime('0000-00-00'));
            }
            if ($form_data['gsq'] && $a = Rxx::convertGsqToDegrees($form_data['gsq'])) {
                $lat =  $a["lat"];
                $lon =  $a["lon"];
                $GSQ =  $a["GSQ"];
            } else {
                $GSQ =  '';
                $lat =  0;
                $lon =  0;
            }
            $region = $countryRepository->getRegionForCountry($form_data['itu']);
            $listener
                ->setGsq($GSQ)
                ->setLat($lat)
                ->setLon($lon)
                ->setRegion($region);
            foreach (static::EDITABLE_FIELDS as $f) {
                $listener->{'set' . ucfirst($f)}($form_data[$f]);
            }

            $em = $this->getDoctrine()->getManager();
            if (!(int)$id) {
                $em->persist($listener);
            }
            $em->flush();
            $id = $listener->getId();
            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }

        $parameters = [
            'id' =>                 $id,
            'fieldGroups' =>        $listenerViewForm->getFieldGroups($isAdmin),
            'form' =>               $form->createView(),
            '_locale' =>            $_locale,
            'mode' =>               ($isAdmin && !$id ? 'Add Listener' : $listener->getName().' &gt; Profile'),
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/profile.html.twig', $parameters);
    }
}
