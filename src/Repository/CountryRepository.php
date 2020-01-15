<?php

namespace App\Repository;

use App\Entity\Itu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CountryRepository extends ServiceEntityRepository
{
    private $sp;

    const RECEIVED_IN_EUROPE = "reu";

    const RECIEVED_IN_NORTH_AMERICA = "rna";

    public function __construct(
        ManagerRegistry $registry,
        StateRepository $sp
    ) {
        parent::__construct($registry, Itu::class);
        $this->sp = $sp;
    }

    public function getMatching(
        $system = false,
        $region = false,
        $havingListeners = false,
        $havingSignals = false,
        $havingStates = false,
        $itu = false
    ) {
        $qb = $this->createQueryBuilder('c');

        if (!$havingSignals) {
            switch ($system) {
                case self::RECEIVED_IN_EUROPE:
                    $qb
                        ->where(
                            $qb->expr()->eq('c.region', ':eu')
                        )
                        ->setParameter('eu', 'eu');
                    break;
                case self::RECIEVED_IN_NORTH_AMERICA:
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
                        ->setParameter('na_ca', ['na', 'ca'])
                        ->setParameter('oc', 'oc')
                        ->setParameter('hwa', 'hwa');
                break;
            }
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

        if ($havingSignals) {
            switch ($system) {
                case self::RECEIVED_IN_EUROPE:
                    $extra = ' where s.heardInEu=1';
                    break;
                case self::RECIEVED_IN_NORTH_AMERICA:
                    $extra = ' where (s.heardInNa=1 or s.heardInCa=1)';
                    break;
                default:
                    $extra = '';
                    break;
            }
            $qb
                ->andWhere(
                    $qb->expr()->in(
                        'c.itu',
                        'SELECT DISTINCT s.itu FROM App\Entity\Signal s'.$extra
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
        $countries = $this->getMatching(
            false,
            false,
            false,
            false,
            true,
            $countryCodes
        );

        foreach ($countries as &$country) {
            $itu =             $country->getItu();
            $country->states =  $this->sp->getStates($itu);
            $country->map =     $this->getMapUrlForCountry($itu);
        }

        return $countries;
    }

    public function getMatchingOptions(
        $system = false,
        $region = false,
        $havingListeners = false,
        $havingSignals = false,
        $withAllOption = false
    ) {
        $countries = $this->getMatching(
            $system,
            $region,
            $havingListeners,
            $havingSignals
        );
        if ($withAllOption) {
            $out = ['(All Countries'.($region ? ' in selected region' : '').')' => ''];
        }
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

    public function getRegionForCountry($code)
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c.region')
            ->where('c.itu = :country')
            ->setParameter(':country', $code)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
