<?php
namespace App\Controller\Web\Cle;

use App\Controller\Web\Base;
use App\Entity\User as UserEntity;
use App\Form\Cle\Cle as CleForm;

use App\Utils\Rxx;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

/**
 * Class Countries
 * @package App\Controller\Web
 */
class Cle extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/cle",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="cle"
     * )
     * @param $_locale
     * @param $system
     * @param Request $request
     * @param CleForm $form
     * @return Response
     */
    public function controller(
        $_locale,
        $system,
        Request $request,
        CleForm $form
    ) {
        $i18n = $this->translator;
        $id = 1;

        $cle = $this->cleRepository->getRecord();
        $simpleFields = [
            'cle',
            'dateTimespan',
            'europeRange1Active',
            'europeRange1Channels',
            'europeRange1FilterOther',
            'europeRange1Itu',
            'europeRange1Locator',
            'europeRange1Low',
            'europeRange1High',
            'europeRange1Sp',
            'europeRange1SpItuClause',
            'europeRange1Recently',
            'europeRange1TextExtra',
            'europeRange1Type',
            'europeRange1Within',
            'europeRange2Active',
            'europeRange2Channels',
            'europeRange2FilterOther',
            'europeRange2Itu',
            'europeRange2Locator',
            'europeRange2Low',
            'europeRange2High',
            'europeRange2Sp',
            'europeRange2SpItuClause',
            'europeRange2Recently',
            'europeRange2TextExtra',
            'europeRange2Type',
            'europeRange2Within',
            'scope',
            'worldRange1Active',
            'worldRange1Channels',
            'worldRange1FilterOther',
            'worldRange1Itu',
            'worldRange1Locator',
            'worldRange1Low',
            'worldRange1High',
            'worldRange1Sp',
            'worldRange1SpItuClause',
            'worldRange1Recently',
            'worldRange1TextExtra',
            'worldRange1Type',
            'worldRange1Within',
            'worldRange2Active',
            'worldRange2Channels',
            'worldRange2FilterOther',
            'worldRange2Itu',
            'worldRange2Locator',
            'worldRange2Low',
            'worldRange2High',
            'worldRange2Sp',
            'worldRange2SpItuClause',
            'worldRange2Recently',
            'worldRange2TextExtra',
            'worldRange2Within',
            'worldRange2Type',
        ];

        $options = [
            'id' =>                     $id,
            'about' =>                  html_entity_decode($cle->getAbout()),
            'additional' =>             html_entity_decode($cle->getAdditional()),
            'dateEnd' =>                ($cle->getDateEnd() ? new DateTime($cle->getDateEnd()->format('Y-m-d')) : null),
            'dateStart' =>              ($cle->getDateStart() ? new DateTime($cle->getDateStart()->format('Y-m-d')) : null),
        ];
        foreach($simpleFields as $f) {
            $options[$f] = $cle->{'get' . ucfirst($f)}();
        }
        $form = $form->buildForm(
            $this->createFormBuilder(),
            $options
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $cle->setAbout($data['about']);
            $cle->setAdditional($data['additional']);
            $cle->setDateStart($data['dateStart']);
            $cle->setDateEnd($data['dateEnd']);
            foreach($simpleFields as $f) {
                $value = $data[$f] === '' ? null : $data[$f];
                $cle->{'set' . ucfirst($f)}($value);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($cle);
            $em->flush();
        }

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       $i18n->trans('CLE %NUMBER%', [ '%NUMBER%' => $cle->getCle() ]),
            'system' =>     $system,
            'admin' =>      (int)$this->parameters['access'] & UserEntity::CLE,
            'cle' =>        $cle,
            'form' =>       $form->createView(),
            'reu1' =>       $this->cleRepository->getUrlForRegion('Europe', 1),
            'reu2' =>       $this->cleRepository->getUrlForRegion('Europe', 2),
            'rww1' =>       $this->cleRepository->getUrlForRegion('World', 1),
            'rww2' =>       $this->cleRepository->getUrlForRegion('World', 2),
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('cle/cle.html.twig', $parameters);
    }
}
