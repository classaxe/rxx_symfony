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
            'cle' =>        $cle
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('cle/cle.html.twig', $parameters);
    }
}
