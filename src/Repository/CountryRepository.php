<?php

namespace App\Repository;

use App\Entity\Itu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CountryRepository extends ServiceEntityRepository
{
    private $sp;

    public function __construct(
        RegistryInterface $registry,
        StateRepository $sp
    ) {
        parent::__construct($registry, Itu::class);
        $this->sp = $sp;
    }

    public function getMatching(
        $system = false,
        $region = false,
        $havingListeners = false,
        $havingStates = false,
        $itu = false
    ) {
        $qb = $this->createQueryBuilder('c');

        switch ($system) {
            case "reu":
                $qb
                    ->where(
                        $qb->expr()->eq('c.region', ':eu')
                    )
                    ->setParameter('eu', 'eu');
                break;
            case "rna":
                $qb
                    ->where(
                        $qb->expr()->orX(
                            $qb->expr()->in('c.region', ':na_ca'),
                            $qb->expr()->andX(
                                $qb->expr()->eq('c.region', ':oc'),
                                $qb->expr()->eq('c.itu', ':hwa')
                            )
                        )
                    )
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa')
                    ;
                break;
        }

        if ($itu) {
            $qb
                ->andWhere(
                    $qb->expr()->in('c.itu', ':itu')
                )
                ->setParameter('itu', explode(',', $itu))
            ;
        }

        if ($region) {
            $qb
                ->andWhere(
                    $qb->expr()->in('c.region', ':region')
                )
                ->setParameter('region', $region)
            ;
        }

        if ($havingListeners) {
            $qb
                ->andWhere(
                    $qb->expr()->in(
                        'c.itu',
                        'SELECT DISTINCT l.itu FROM App\Entity\Listener l'
                    )
                );
        }

        if ($havingStates) {
            $qb
                ->andWhere(
                    $qb->expr()->in('c.hasSp', '1')
                );
        }

        return $qb
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function getCountriesAndStates($countryCodes = null)
    {
        $countries = $this->getMatching(false, false, false, true, $countryCodes);

        foreach ($countries as &$country) {
            $itu =             $country->getItu();
            $country->states =  $this->sp->getStates($itu);
            $country->map =     $this->getMapUrlForCountry($itu);
        }

        return $countries;
    }

    public function getMatchingOptions($system = false, $region = false, $havingListeners = false)
    {
        $countries = $this->getMatching($system, $region, $havingListeners);
        $out = ['(All Countries'.($region ? ' in selected region' : '').')' => ''];
        foreach ($countries as $row) {
            $out[$row->getName()] = $row->getItu();
        }

        return $out;
    }

    public function getMapUrlForCountry($code)
    {
        switch ($code) {
            case "AUS":
                return 'au';
            case "CAN":
                return 'na';
            case "USA":
                return 'na';
            default:
                return false;
        }
    }
}
