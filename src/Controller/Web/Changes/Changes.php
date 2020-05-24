<?php
namespace App\Controller\Web\Changes;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Changes extends Base
{
    /**
     * @Route(
     *     "/{_locale}/{system}/changes",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="changes"
     * )
     * @param $_locale
     * @param $system
     * @return Response
     */
    public function controller($_locale, $system)
    {
        $entries =      $this->systemRepository->getGitInfo();
        $tweaks = [
            [
                'Anders H',
                'Patric',
                'Peter Conway',
                'Scott W',
                'Vernon',
                'Vic Metcalfe',
                '[',
                ']'
            ],
            [
                '[Anders H]',
                '[Patric]',
                '[Peter Conway]',
                '[Scott W]',
                '[Vernon]',
                '[Vic Metcalfe]',
                '<span>',
                '</span>'
            ]
        ];
        $changelog =
            "<ul class='changelog'><li>"
            . str_replace($tweaks[0], $tweaks[1], implode("</li>\n<li>", $entries))
            . "</li></ul>";

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Change Log',
            'system' =>     $system,
            'classic' =>    $this->systemRepository->getClassicUrl('changes'),
            'changelog' =>  $changelog,
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('changes/index.html.twig', $parameters);
    }

}
