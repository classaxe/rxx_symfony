<?php
namespace App\Controller\Web\Donate;

use App\Controller\Web\Base;
use App\Utils\Rxx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Donate extends Base
{
    const MONTHLY_COST =    '55.29';
    const DOMAIN_COST =     '20.33';
    const ANNUAL_COST =     (12 * self::MONTHLY_COST) + self::DOMAIN_COST;
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
            'monthly' =>    static::MONTHLY_COST,
            'annual' =>     static::ANNUAL_COST,
            'donations' =>  $this->donationRepository->getDonationsPublic()
        ];

        //print Rxx::y($parameters['donations']); die;
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('donate/index.html.twig', $parameters);
    }

}
