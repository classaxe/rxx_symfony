<?php
namespace App\Controller\Web;

use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultMode
 * @package App\Controller\Web
 */
class DefaultMode extends AbstractController
{
    /**
     * @Route(
     *     "/{locale}/{system}/",
     *     requirements={
     *        "locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="system"
     * )
     * @param $system
     */
    public function defaultModeController(
        $locale,
        $system
    ) {
        $parameters =[
            'locale' => $locale,
            'system' => $system
        ];

        return $this->redirectToRoute("signals", $parameters);
    }
}
