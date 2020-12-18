<?php
namespace App\Controller\Web\Changes;

use App\Controller\Web\Base;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations

class Changes extends Base
{
    const FIRST_COMMIT = '2018-06-08';

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
        $entries =      $this->systemRepository->getGitInfo($this->systemRepository::NEW_VERSION_AGE);
        $tweaks = [
            [
                'Anders H',
                'Patric',
                'Peter Conway',
                'Scott W',
                'Vernon Matteson',
                'Vic Metcalfe',
                '[[',
                ']]',
                '[',
                ']'
            ],
            [
                '[Anders H]',
                '[Patric]',
                '[Peter Conway]',
                '[Scott W]',
                '[Vernon Matteson]',
                '[Vic Metcalfe]',
                '[',
                ']',
                '<span>',
                '</span>'
            ]
        ];
        $changelog =
            "<ul class='changelog'>"
            . str_replace($tweaks[0], $tweaks[1], implode("\n", $entries))
            . "</ul>";

        $parameters = [
            '_locale' =>    $_locale,
            'mode' =>       'Change Log',
            'system' =>     $system,
            'classic' =>    $this->systemRepository->getClassicUrl('changes'),
            'changelog' =>  $changelog,
            'count' =>      count($entries),
            'first' =>      static::FIRST_COMMIT
        ];

        $parameters = array_merge($parameters, $this->parameters);
        return $this->render('changes/index.html.twig', $parameters);
    }

}
