<?php

namespace App\Repository;

use App\Entity\Cle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CleRepository extends ServiceEntityRepository
{
    private $cle;
    /**
     * CleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cle::class);
        $this->cle = $this->find(1);
    }

    public function getRecord() {
        return $this->cle;
    }

    /**
     * @param $region
     * @param $range
     * @return string
     */
    public function getUrlForRegion($region, $range) {
        $params = [];
        $cle = $this->cle;
        $prefix = "get{$region}Range$range";
        if ($val = $cle->{ $prefix . 'Type'}()) {
            $types = explode('&amp;', str_replace([ 'type_', '=1' ], '', $val));
            if (implode(',', $types) !== 'NDB') {
                $params[] = 'types=' . implode(',', $types);
            }
        }
        if (($val1 = $cle->{ $prefix . 'Low'}()) && ($val2 = $cle->{ $prefix . 'High'}())) {
            $params[] = 'khz=' . $val1 . ',' . $val2;
        }
        if ($val = $cle->{ $prefix . 'Channels'}()) {
            $params[] = 'channels=' . $val;
        }
        if ($val = $cle->{ $prefix . 'Locator'}()) {
            $params[] = 'gsq=' . urlencode($val);
        }
        if ($val = $cle->{ $prefix . 'Itu'}()) {
            $params[] = 'countries=' . urlencode($val);
        }
        if ($val = $cle->{ $prefix . 'Sp'}()) {
            $params[] = 'states=' . urlencode($val);
        }
        if (($val = $cle->{ $prefix . 'SpItuClause'}()) && $cle->{ $prefix . 'Itu'}() && $cle->{ $prefix . 'Sp'}()) {
            $params[] = 'sp_itu_clause=' . urlencode($val);
        }
        if (($val = $cle->{ $prefix . 'Recently'}())) {
            $params[] = 'recently=' . urlencode($val);
        }
        if (($val = $cle->{ $prefix . 'Within'}())) {
            $params[] = 'within=' . urlencode($val);
        }
        if (($val = $cle->{ $prefix . 'Active'}())) {
            $params[] = 'active=' . urlencode($val);
        }
        if ($val = $cle->{ $prefix . 'FilterOther'}()) {
            $args = explode('&', $val);
            foreach ($args as $arg) {
                $params[] = $arg;
            }
        }
        return implode('&', $params);
    }

    public function getUrlsForSystems() {
        return [
            'reu' => [
                $this->getUrlForRegion('Europe', 1),
                $this->getUrlForRegion('Europe', 2)
            ],
            'rna' => [
                $this->getUrlForRegion('World', 1),
                $this->getUrlForRegion('World', 2)
            ],
            'rww' => [
                $this->getUrlForRegion('World', 1),
                $this->getUrlForRegion('World', 2)
            ],
        ];
    }

}
