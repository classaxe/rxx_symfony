<?php
namespace App\Controller\Web\Listener;

use App\Controller\Web\Base;
use App\Entity\Listener as ListenerEntity;
use App\Form\Listener\Weather as ListenerWeatherForm;
use App\Repository\ListenerRepository;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Listeners
 * @package App\Controller\Web\Listener
 */
class Weather extends Base
{

    /**
     * @Route(
     *     "/{system}/listeners/{id}/weather",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_weather"
     * )
     */
    public function weatherController(
        $system,
        $id,
        Request $request,
        ListenerWeatherForm $listenerWeatherForm,
        ListenerRepository $listenerRepository
    ) {
        if ($id !== 'new' && (int) $id) {
            $listener = $listenerRepository->find((int)$id);
            if (!$listener) {
                return $this->redirectToRoute('listeners', ['system' => $system]);
            }
        } else {
            $listener = false;
        }
        $isAdmin = $this->parameters['isAdmin'];
        if (!$listener && !$isAdmin) {
            return $this->redirectToRoute('listeners', ['system' => $system]);
        }
        $options = [
            'hours'     =>  '12',
            'id'        =>  $listener ? $id : '',
            'name'      =>  ''
        ];
        $form = $listenerWeatherForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted()) {
            $form_data = $form->getData();
            $data['form'] = $form_data;
            if ((int)$id) {
                $listener = $listenerRepository->find($id);
            } else {
                $listener = new ListenerEntity();
                $listener
                    ->setLogLatest(\App\Utils\Rxx::getUtcDateTime('0000-00-00'));
            }
            $listener
                ->setCallsign($form_data['callsign'])
                ->setEmail($form_data['email'])
                ->setEquipment($form_data['equipment'])
                ->setGsq($form_data['gsq'])
                ->setItu($form_data['itu'])
                ->setMapX($form_data['mapX'])
                ->setMapY($form_data['mapY'])
                ->setName($form_data['name'])
                ->setNotes($form_data['notes'])
                ->setPrimaryQth($form_data['primary'])
                ->setQth($form_data['qth'])
                ->setSp($form_data['sp'])
                ->setTimezone($form_data['timezone'])
                ->setWebsite($form_data['website'])
            ;
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
            'fieldGroups' =>        $listenerWeatherForm->getFieldGroups($isAdmin),
            'form' =>               $form->createView(),
            'mode' =>               $listener->getName().' &gt; Weather',
            'system' =>             $system,
            'tabs' =>               $listenerRepository->getTabs($listener),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/profile.html.twig', $parameters);
    }
}
