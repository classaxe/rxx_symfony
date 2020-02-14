<?php
namespace App\Controller\Web;

use App\Repository\CleRepository;
use App\Repository\MapRepository;
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
     * @param $area
     * @param MapRepository $mapRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function controller($_locale, $system, CleRepository $cleRepository)
    {
        $i18n = $this->translator;

        $cle = $cleRepository->find(1);

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       $i18n->trans('CLE %NUMBER%', [ '%NUMBER%' => $cle->getCle() ]),
            'system' =>     $system,
            'cle' =>        $cle,
            'reu1' =>       $this->getUrlForRegion($cle, 'Europe', 1),
            'reu2' =>       $this->getUrlForRegion($cle, 'Europe', 2),
            'rww1' =>       $this->getUrlForRegion($cle, 'World', 1),
            'rww2' =>       $this->getUrlForRegion($cle, 'World', 2),
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('cle/cle.html.twig', $parameters);
    }

    private function getUrlForRegion($cle, $region, $range) {
        $params = [];
        $prefix = "get{$region}Range$range";
        if ($val = $cle->{ $prefix . 'Low'}()) {
            $params[] = 'khz_1=' . $val;
        }
        if ($val = $cle->{ $prefix . 'High'}()) {
            $params[] = 'khz_2=' . $val;
        }
        return implode('&', $params);
    }
}
