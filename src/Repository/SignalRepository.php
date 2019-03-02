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

    private function addFilterCall(&$qb, $call)
    {
        $qb
            ->andWhere('(s.call LIKE :like_call)')
            ->setParameter('like_call', '%'.$call.'%');
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

        $qb
            ->addSelect(
                "(CASE WHEN s.active = 0 THEN 1 ELSE 0 END) AS _active"
            );

          if ($this->signalsColumns[$args['sort']]['sort']) {
            $qb
                ->addSelect(
                    "(CASE WHEN (".$this->signalsColumns[$args['sort']]['sort'].")='' THEN 1 ELSE 0 END) AS _blank"
                );
        }

        if ($args['call'] !== null) {
            $qb
                ->addSelect(
                    "(CASE WHEN s.call = :call THEN 1 ELSE 0 END) AS _call"
                )
                ->setParameter('call', $args['call']);
        }

        $this->addFilterSystem($qb, $system);
        $this->addFilterTypes($qb, $args['signalTypes']);
        $this->addFilterCall($qb, $args['call']);

        if ($args['country'] !== '') {
            $qb
                ->andWhere('(s.itu = :country)')
                ->setParameter('country', $args['country']);
        }

        if (isset($args['region']) && $args['region'] !== '') {
            $qb
                ->andWhere('(s.region = :region)')
                ->setParameter('region', $args['region']);
        }

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults($args['limit']);
        }

        if ($args['call'] !== null) {
            $qb
                ->addOrderBy(
                    '_call',
                    'DESC'
                );

        }

        $qb
            ->addOrderBy(
                '_active',
                'ASC'
            );

        if ($this->signalsColumns[$args['sort']]['sort']) {
            $qb
                ->addOrderBy(
                    '_blank',
                    'DESC'
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
        $this->addFilterCall($qb, $args['call']);

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
