<?php
namespace App\Controller\Web\Donate;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Donate extends Base
{
    const MONTHLY_COST =    '$35 CAD';
    const DOMAIN_COST =     '$14.70 CAD';
    /**
     * @Route(
     *     "/{_locale}/{system}/donate",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="donate"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function controller($_locale, $system)
    {
        $admins = array_keys($this->systemRepository->getAdmins());

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Donate',
            'system' =>     $system,
            'admins' =>     $admins,
            'domain' =>     static::DOMAIN_COST,
            'monthly' =>    static::MONTHLY_COST
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('donate/index.html.twig', $parameters);
    }

}
