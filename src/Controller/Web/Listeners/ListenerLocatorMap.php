<?php
namespace App\Controller\Web\Listeners;

use App\Form\Listeners\ListenerLocatorMap as ListenerLocatorMapForm;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Listeners
 * @package App\Controller\Web\Listeners
 */
class ListenerLocatorMap extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/locatormap",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_locatormap"
     * )
     * @param $_locale
     * @param $system
     * @param $id
     * @param Request $request
     * @param ListenerLocatorMapForm $ListenerLocatorMapForm
     * @return RedirectResponse|Response
     */
    public function controller(
        $_locale,
        $system,
        $id,
        Request $request,
        ListenerLocatorMapForm $ListenerLocatorMapForm
    ) {
        $isAdmin = $this->parameters['isAdmin'];

        if (!$isAdmin || !$listener = $this->getValidListener($id)) {
            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }
        $map = ('HWA' === $listener->getItu() ?
            'na' : ('ca' === $listener->getRegion() ? 'na' : $listener->getRegion())
        );
        if (!in_array($map, ['eu', 'na'])) {
            return $this->redirectToRoute('listener', ['system' => $system, 'id' => $id]);
        }

        $options = [
            'id' =>         $listener->getId(),
            'mapX' =>       $listener->getMapX(),
            'mapY' =>       $listener->getMapY()
        ];
        $form = $ListenerLocatorMapForm->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($isAdmin && $form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $listener
                ->setMapX($data['mapX'])
                ->setMapY($data['mapY']);
            $em = $this->getDoctrine()->getManager();
            if (!(int)$id) {
                $em->persist($listener);
            }
            $em->flush();
            $id = $listener->getId();
            return $this->redirectToRoute('listener_locatormap', ['system' => $system, 'id' => $id]);
        }

        $i18n =     $this->translator;
        $title =    $i18n->trans('Locator Map for %s');
        $parameters = [
            '_locale' =>            $_locale,
            'form' =>               $form->createView(),
            'id' =>                 $id,
            'l' =>                  $listener,
            'map' =>                $map,
            'mode' =>               sprintf($title, $listener->getFormattedNameAndLocation()),
            'system' =>             $system,
            'tabs' =>               $this->listenerRepository->getTabs($listener, $isAdmin),
        ];
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('listener/locatormap.html.twig', $parameters);
    }

    /**
     * @Route(
     *     "/{_locale}/{system}/listeners/{id}/locatormap/image/{map}",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww",
     *        "map": "eu|na"
     *     },
     *     name="listener_locatormap_image"
     * )
     * @param $id
     * @param $map
     * @return void
     */
    public function image(
        $id,
        $map
    ) {
        $isAdmin = $this->parameters['isAdmin'];

        if (!$isAdmin || !$listener = $this->getValidListener($id)) {
            throw $this->createNotFoundException('The listener does not exist');
        }

        header('Content-Type: image/gif');
        $this->mapRepository->drawMapImage($map, 'countries');
        die;
    }

}
