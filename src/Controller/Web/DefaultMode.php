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
     *     "/{_locale}/{system}/",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *        "system": "reu|rna|rww"
     *     },
     *     name="mode"
     * )
     */
    public function defaultModeController(
        $_locale,
        $system
    ) {
        $parameters =[
            '_locale' =>    $_locale,
            'system' =>     $system
        ];

        return $this->redirectToRoute("signals", $parameters);
    }
}
