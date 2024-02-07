<?php
namespace App\Controller\Web\Donate;

use App\Controller\Web\Base;
use App\Utils\Rxx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Donate extends Base
{
    private $MONTHLY_COST;
    private $DOMAIN_COST;
    private $ANNUAL_COST;
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

        $this->MONTHLY_COST =   getenv('MONTHLY_COST');
        $this->DOMAIN_COST =    getenv('DOMAIN_COST');
        $this->ANNUAL_COST =    (12 * $this->MONTHLY_COST) + $this->DOMAIN_COST;

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Donate',
            'system' =>     $system,
            'admins' =>     $admins,
            'domain' =>     $this->DOMAIN_COST,
            'monthly' =>    $this->MONTHLY_COST,
            'annual' =>     $this->ANNUAL_COST,
            'donations' =>  $this->donationRepository->getDonationsPublic(),
            'year' =>   $this->donationRepository->getDonationsYear(),
        ];

        //print Rxx::y($parameters['donations']); die;
        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('donate/index.html.twig', $parameters);
    }

}
