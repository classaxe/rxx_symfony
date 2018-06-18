<?php

namespace App\Repository;

use App\Entity\Itu;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ItuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Itu::class);
    }

    public function getAllCountriesForSystem($system)
    {
        $qb = $this->createQueryBuilder('c');
        switch($system) {
            case "reu":
                $qb
                    ->andWhere('c.region = :eu')
                    ->setParameter('eu','eu');
                break;
            case "rna":
                $qb
                    ->andWhere('c.region = :oc')
                    ->andWhere('c.itu = :hwa')
                    ->orWhere('c.region in (:na_ca)')
                    ->setParameter('na_ca', array('na','ca'))
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }

        return $qb
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function getAllCountryOptionsForSystem($system)
    {
        $countries = $this->getAllCountriesForSystem($system);
        $out = ['(All Countries' => ''];
        foreach ($countries as $row) {
            $out[$row->getName()] = $row->getItu();
        }
        return $out;
    }

/*
    public function getAvailableRooms($date_start, $date_final)
	{
    	$em = $this->getEntityManager();
    	$qb = $em->createQueryBuilder();

    	$nots = $em->createQuery("
    	SELECT IDENTITY(b.room) FROM AppBundle:Reservation b
        	WHERE NOT (b.dateOut   < '$date_start'
           	OR
           	b.dateIn > '$date_final')
    	");

    	$dql_query = $nots->getDQL();
    	$qb->resetDQLParts();


    	$query = $qb->select('r')
                	->from('AppBundle:Room', 'r')
                	->where($qb->expr()->notIn('r.id', $dql_query ))
                	->getQuery()
                	->getResult();

    	try {

        	return $query;
    	} catch (NoResultException $e) {
        	return null;
    	}
	}
*/
}
