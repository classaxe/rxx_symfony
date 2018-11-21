<?php

namespace App\Repository;

use App\Columns\Signals as SignalsColumns;
use App\Entity\Signal;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SignalRepository extends ServiceEntityRepository
{

    private $signalsColumns;

    public function __construct(
        RegistryInterface $registry,
        SignalsColumns $signalsColumns

    ) {
        parent::__construct($registry, Signal::class);
        $this->signalsColumns = $signalsColumns->getColumns();
    }

    private function addFilterSystem(&$qb, $system)
    {
        switch ($system) {
            case "reu":
                $qb
                    ->andWhere('(s.heardInEu = 1)');
                break;
            case "rna":
                $qb
                    ->andWhere('(s.heardInNa = 1) or (s.heardInCa = 1)');
                break;
        }
    }

    private function addFilterTypes(&$qb, $types)
    {
        $qb
            ->andWhere('(s.type IN(:types))')
            ->setParameter('types', $types);
    }

    public function getColumns()
    {
        return $this->signalsColumns;
    }

    public function getFilteredSignals($system, $args)
    {
        $qb =
            $this
                ->createQueryBuilder('s')
                ->select('s')
        ;

        if ($this->signalsColumns[$args['sort']]['sort']) {
            $qb
                ->addSelect(
                    "(CASE WHEN (".$this->signalsColumns[$args['sort']]['sort'].")='' THEN 1 ELSE 0 END) AS _blank"
                );
        }

        $this->addFilterSystem($qb, $system);
        $this->addFilterTypes($qb, $args['signalTypes']);

        if ($args['country']) {
            $qb
                ->andWhere('(s.itu = :country)')
                ->setParameter('country', $args['country']);
        }

//        if (isset($args['region']) && $args['region']) {
//            $qb
//                ->andWhere('(s.region = :region)')
//                ->setParameter('region', $args['region']);
//        }

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults($args['limit']);
        }

        if ($this->signalsColumns[$args['sort']]['sort']) {
            $qb
                ->orderBy(
                    '_blank',
                    'ASC'
                )
                ->addOrderBy(
                    ($this->signalsColumns[$args['sort']]['sort']),
                    ($args['order'] == 'd' ? 'DESC' : 'ASC')
                );
        }

        $result = $qb->getQuery()->execute();
//        print Rxx::y($result); die;

        // Necessary to resolve extra nesting in results caused by extra select to ignore empty fields in sort order
        $out = [];
        foreach ($result as $key => $value) {
            $out[] = $value[0];
        }
        return $out;
    }

    public function getFilteredSignalsCount($system, $args)
    {
        $qb =
            $this->createQueryBuilder('s')
                ->select('COUNT(s.id) as count')

        ;

//        print Rxx::y($args); die;


        $this->addFilterSystem($qb, $system);
        $this->addFilterTypes($qb, $args['signalTypes']);

        if ($args['country']) {
            $qb
                ->andWhere('(s.itu = :country)')
                ->setParameter('country', $args['country']);
        }
        if (isset($args['region']) && $args['region']) {
            $qb
                ->andWhere('(s.region = :region)')
                ->setParameter('region', $args['region']);
        }
        $result = $qb->getQuery()->execute();
        return $result[0]['count'];
    }

}
