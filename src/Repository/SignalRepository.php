<?php

namespace App\Repository;

use App\Columns\Signals as SignalsColumns;
use App\Entity\Signal;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SignalRepository extends ServiceEntityRepository
{
    private $queryBuilder;
    private $signalsColumns;

    public function __construct(
        RegistryInterface $registry,
        SignalsColumns $signalsColumns

    ) {
        parent::__construct($registry, Signal::class);
        $this->signalsColumns = $signalsColumns->getColumns();
    }

    private function addFilterCall($call)
    {
        if (isset($call) && $call !== '') {
            $this
                ->getQueryBuilder()
                ->andWhere('(s.call LIKE :like_call)')
                ->setParameter('like_call', '%'.$call.'%');
        }
        return $this;
    }

    private function addFilterChannels($channels)
    {
        switch ($channels) {
            case 1:
                $this
                    ->getQueryBuilder()
                    ->andWhere('MOD(s.khz * 1000, 1000) = 0');
                break;
            case 2:
                $this
                    ->getQueryBuilder()
                    ->andWhere('MOD(s.khz * 1000, 1000) != 0');
                break;
        }
        return $this;
    }

    private function addFilterCountries($countries)
    {
        if (isset($countries) && $countries !== '') {
            $countries = explode(" ", str_replace('*', '%', $countries));
            $this
                ->getQueryBuilder()
                ->andWhere("s.itu LIKE '" . implode($countries, "' OR s.itu LIKE '") . "'");
        }
        return $this;
    }

    private function addFilterFreq($khz_1, $khz_2)
    {
        $khz_1 = (float)$khz_1 ? (float)$khz_1 : 0;
        $khz_2 = (float)$khz_2 ? (float)$khz_2 : 1000000;

        if ($khz_1 !== 0 || $khz_2 !== 1000000) {
            $this
                ->getQueryBuilder()
                ->andWhere('(s.khz BETWEEN :khz1 AND :khz2)')
                ->setParameter('khz1', $khz_1)
                ->setParameter('khz2', $khz_2);
        }
        return $this;
    }

    private function addFilterRegion($region)
    {
        if (isset($region) && $region !== '') {
            $this
                ->getQueryBuilder()
                ->andWhere('(s.region = :region)')
                ->setParameter('region', $region);
        }
        return $this;
    }

    private function addFilterStates($states)
    {
        if (isset($states) && $states !== '') {
            $states = explode(" ", str_replace('*', '%', $states));
            $this
                ->getQueryBuilder()
                ->andWhere("s.sp LIKE '" . implode($states, "' OR s.sp LIKE '") . "'");
        }
        return $this;
    }

    private function addFilterSystem($system)
    {
        switch ($system) {
            case "reu":
                $this
                    ->getQueryBuilder()
                    ->andWhere('(s.heardInEu = 1)');
                break;
            case "rna":
                $this
                    ->getQueryBuilder()
                    ->andWhere('(s.heardInNa = 1) or (s.heardInCa = 1)');
                break;
        }
        return $this;
    }

    private function addFilterTypes($types)
    {
        $this
            ->getQueryBuilder()
            ->andWhere('(s.type IN(:types))')
            ->setParameter('types', $types);
        return $this;
    }

    public function getColumns()
    {
        return $this->signalsColumns;
    }

    private function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    public function getFilteredSignals($system, $args)
    {
        $this->setQueryBuilder(
            $this
                ->createQueryBuilder('s')
                ->select('s')
                ->addSelect(
                    "(CASE WHEN s.active = 0 THEN 1 ELSE 0 END) AS _active"
                )
        );
        if ($this->signalsColumns[$args['sort']]['sort']) {
            $this
                ->getQueryBuilder()
                ->addSelect(
                    "(CASE WHEN (".$this->signalsColumns[$args['sort']]['sort'].")='' THEN 1 ELSE 0 END) AS _blank"
                );
        }

        if ($args['call'] !== null) {
            $this
                ->getQueryBuilder()
                ->addSelect(
                    "(CASE WHEN s.call = :call THEN 1 ELSE 0 END) AS _call"
                )
                ->setParameter('call', $args['call']);
        }

        $this
            ->addFilterSystem($system)
            ->addFilterTypes($args['signalTypes'])
            ->addFilterCall($args['call'])
            ->addFilterChannels($args['channels'])
            ->addFilterFreq($args['khz_1'], $args['khz_2'])
            ->addFilterCountries($args['countries'])
            ->addFilterStates($args['states'])
            ->addFilterRegion($args['region']);

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $this
                ->getQueryBuilder()
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults($args['limit']);
        }

        if ($args['call'] !== null) {
            $this
                ->getQueryBuilder()
                ->addOrderBy(
                    '_call',
                    'DESC'
                );

        }

        $this
            ->getQueryBuilder()
            ->addOrderBy(
                '_active',
                'ASC'
            );

        if ($this->signalsColumns[$args['sort']]['sort']) {
            $this
                ->getQueryBuilder()
                ->addOrderBy(
                    '_blank',
                    'DESC'
                )
                ->addOrderBy(
                    ($this->signalsColumns[$args['sort']]['sort']),
                    ($args['order'] == 'd' ? 'DESC' : 'ASC')
                );
        }

        $result =
            $this
                ->getQueryBuilder()
                ->getQuery()
                ->execute();
//        print Rxx::y($result);

        // Necessary to resolve extra nesting in results caused by extra select to ignore empty fields in sort order
        $out = [];
        foreach ($result as $key => $value) {
            $out[] = $value[0];
        }
        return $out;
    }

    public function getFilteredSignalsCount($system, $args)
    {
        $this
            ->setQueryBuilder(
                $this
                    ->createQueryBuilder('s')
                    ->select('COUNT(s.id) as count')
            )
            ->addFilterSystem($system)
            ->addFilterTypes($args['signalTypes'])
            ->addFilterCall($args['call'])
            ->addFilterChannels($args['channels'])
            ->addFilterFreq($args['khz_1'], $args['khz_2'])
            ->addFilterCountries($args['countries'])
            ->addFilterStates($args['states'])
            ->addFilterRegion($args['region']);

        $result = $this->getQueryBuilder()->getQuery()->execute();
        return $result[0]['count'];
    }

    private function setQueryBuilder($qb)
    {
        $this->queryBuilder = $qb;
        return $this;
    }

}
