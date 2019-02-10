<?php
namespace App\Controller\Web;

use App\Service\GeoService;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultSystem
 * @package App\Controller\Web
 */
class DefaultSystem extends AbstractController
{
    /**
     * @var GeoService
     */
    private $geoService;

    /**
     * DefaultSystem constructor.
     * @param GeoService $GeoService
     */
    public function __construct(GeoService $GeoService)
    {
        $this->geoService = $GeoService;
    }

    /**
     * @Route(
     *     "/{_locale}/",
     *     requirements={
     *        "_locale": "de|en|es|fr",
     *     },
     *     name="system"
     * )
     */
    public function defaultSystemController($_locale)
    {
        $parameters = [
            '_locale' => $_locale,
            'system' => $this->geoService->getDefaultSystem()
        ];

        return $this->redirectToRoute('mode', $parameters);
    }
}
