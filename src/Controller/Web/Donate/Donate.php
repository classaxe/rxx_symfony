<?php
namespace App\Controller\Web\Donate;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Donate extends Base
{
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
        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Donate',
            'system' =>     $system,
            'classic' =>    $this->systemRepository->getClassicUrl('donate')
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('donate/index.html.twig', $parameters);
    }

}
