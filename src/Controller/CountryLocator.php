<?php
namespace App\Controller;
use App\Utils\Rxx;
use App\Entity\Itu;
use App\Entity\Region;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CountryLocator extends Controller {

    private $baseUrl;
    private $selfUrl;

    /**
     * @Route(
     *     "/{system}/show_itu/{filter}",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     defaults={"filter"=""},
     *     name="show_itu"
     * )
     */
    public function countryLocatorController($system, $filter)
    {
        $this->baseUrl = $this->generateUrl('system', array('system' => $system));
        $this->selfUrl = $this->generateUrl('show_itu', array('system' => $system));
        $regions = $this->getRegions($filter);
        foreach($regions as &$region) {
            $code = $region->getRegion();
            $region->countries = $this->getCountries($code);
            $region->map = $this->getMapUrlForRegion($code);
        }
        return $this->render(
            'countries/index.html.twig',
            array(
                'system' => $system,
                'mode' => 'Country Code Locator',
                'regions' => $regions
            )
        );
    }

    private function getRegions($regions = null) {
        $filter = ($regions ? ['region' => explode(',', $regions)] : []);
        return
            $this->getDoctrine()
                ->getRepository(Region::class)
                ->findBy($filter, ['id' => 'ASC']);
    }

    private function getCountries($regions = null) {
        $filter = ($regions ? ['region' => explode(',', $regions)] : []);
        return
            $this->getDoctrine()
                ->getRepository(Itu::class)
                ->findBy($filter, ['name' => 'ASC']);
    }

    private function getMapUrlForRegion($region) {
        switch($region) {
            case "af":
                return '/dx/images/af_map.gif';
                break;
            case "au":
                return '/dx/images/au_map.gif';
                break;
            case "ca":
                return $this->baseUrl.'generate_map_na';
                break;
            case "eu":
                return $this->baseUrl.'generate_map_eu';
                break;
            case "na":
                return $this->baseUrl.'generate_map_na';
                break;
            case "sa":
                return '/dx/images/sa_map.gif';
                break;
            default:
                return false;
                break;
        }

    }

}