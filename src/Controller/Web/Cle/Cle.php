<?php
namespace App\Controller\Web\Cle;

use App\Controller\Web\Base;
use App\Entity\User as UserEntity;
use App\Form\Cle\Cle as CleForm;

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

        $cle = $this->cleRepository->find($id);
        $simpleFields = [
            'cle',
            'dateTimespan',
            'europeRange1Channels',
            'europeRange1FilterOther',
            'europeRange1Itu',
            'europeRange1Locator',
            'europeRange1Low',
            'europeRange1High',
            'europeRange1Sp',
            'europeRange1SpItuClause',
            'europeRange1TextExtra',
            'europeRange1Type',
            'europeRange2Channels',
            'europeRange2FilterOther',
            'europeRange2Itu',
            'europeRange2Locator',
            'europeRange2Low',
            'europeRange2High',
            'europeRange2Sp',
            'europeRange2SpItuClause',
            'europeRange2TextExtra',
            'europeRange2Type',
            'scope',
            'worldRange1Channels',
            'worldRange1FilterOther',
            'worldRange1Itu',
            'worldRange1Locator',
            'worldRange1Low',
            'worldRange1High',
            'worldRange1Sp',
            'worldRange1SpItuClause',
            'worldRange1TextExtra',
            'worldRange1Type',
            'worldRange2Channels',
            'worldRange2FilterOther',
            'worldRange2Itu',
            'worldRange2Locator',
            'worldRange2Low',
            'worldRange2High',
            'worldRange2Sp',
            'worldRange2SpItuClause',
            'worldRange2TextExtra',
            'worldRange2Type',
        ];

        $options = [
            'id' =>                     $id,
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
            'classic' =>    $this->systemRepository->getClassicUrl('cle'),
            'admin' =>      (int)$this->parameters['access'] & UserEntity::CLE,
            'cle' =>        $cle,
            'form' =>       $form->createView(),
            'reu1' =>       $this->getUrlForRegion($cle, 'Europe', 1),
            'reu2' =>       $this->getUrlForRegion($cle, 'Europe', 2),
            'rww1' =>       $this->getUrlForRegion($cle, 'World', 1),
            'rww2' =>       $this->getUrlForRegion($cle, 'World', 2),
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('cle/cle.html.twig', $parameters);
    }

    /**
     * @param $cle
     * @param $region
     * @param $range
     * @return string
     */
    private function getUrlForRegion($cle, $region, $range) {
        $params = [];
        $prefix = "get{$region}Range$range";
        if (($val1 = $cle->{ $prefix . 'Low'}()) && ($val2 = $cle->{ $prefix . 'High'}())) {
            $params[] = 'khz=' . $val1 . ',' . $val2;
        }
        if ($val = $cle->{ $prefix . 'Channels'}()) {
            $params[] = 'channels=' . $val;
        }
        if ($val = $cle->{ $prefix . 'Type'}()) {
            $types = explode('&amp;', str_replace([ 'type_', '=1' ], '', $val));
            $params[] = 'types=' . implode(',', $types);
        }
        if ($val = $cle->{ $prefix . 'Locator'}()) {
            $params[] = 'gsq=' . urlencode($val);
        }
        if ($val = $cle->{ $prefix . 'Itu'}()) {
            $params[] = 'countries=' . urlencode($val);
        }
        if ($val = $cle->{ $prefix . 'Sp'}()) {
            $params[] = 'states=' . urlencode($val);
        }
        if (($val = $cle->{ $prefix . 'SpItuClause'}()) && $cle->{ $prefix . 'Itu'}() && $cle->{ $prefix . 'Sp'}()) {
            $params[] = 'sp_itu_clause=' . urlencode($val);
        }
        if ($val = $cle->{ $prefix . 'FilterOther'}()) {
            $args = explode('&', $val);
            foreach ($args as $arg) {
                $params[] = $arg;
            }
        }
        return implode('&', $params);
    }
}
