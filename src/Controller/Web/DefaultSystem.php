<?php
namespace App\Controller\Web;

use App\Service\GeoService;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultSystem
 * @package App\Controller\Web
 */
class DefaultSystem extends Controller
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
     *     "/",
     *     name="home"
     * )
     */
    public function defaultSystemController()
    {
        $parameters = ['system' => $this->geoService->getDefaultSystem()];

        return $this->redirectToRoute("system", $parameters);
    }
}
