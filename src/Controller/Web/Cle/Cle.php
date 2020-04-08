<?php
namespace App\Controller\Web\Cle;

use App\Controller\Web\Base;
use App\Repository\CleRepository;
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
     * @param CleRepository $cleRepository
     * @return Response
     */
    public function controller($_locale, $system, CleRepository $cleRepository)
    {
        $i18n = $this->translator;

        $cle = $cleRepository->find(1);

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       $i18n->trans('CLE %NUMBER%', [ '%NUMBER%' => $cle->getCle() ]),
            'system' =>     $system,
            'classic' =>    $this->systemRepository->getClassicUrl('cle'),
            'cle' =>        $cle,
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
