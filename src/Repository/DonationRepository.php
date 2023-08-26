<?php

namespace App\Repository;

use App\Entity\Donation;
use App\Columns\Donations as DonationsColumns;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Donation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donation[]    findAll()
 * @method Donation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonationRepository extends ServiceEntityRepository
{

    private $connection;
    private $donationsColumns;
    
    private $tabs = [
        ['donation', 'Details'],
 //       ['donor_donations', 'Donations (%%donations%%)'],
    ];


    /**
     * @param Connection $connection
     * @param ManagerRegistry $registry
     **/
    public function __construct(
        Connection $connection,
        ManagerRegistry $registry,
        DonationsColumns $donationsColumns
    ) {
        $this->connection = $connection;
        $this->donationsColumns = $donationsColumns;
        parent::__construct($registry, Donation::class);
    }

    public function getColumns()
    {
        return $this->donationsColumns->getColumns();
    }

    public function getCount()
    {
        return $this
            ->createQueryBuilder('d')
            ->select('count(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRecords($args)
    {
        $qb = $this
            ->createQueryBuilder('d')
            ->select('d.id, d.name, d.date, d.amount, d.message, donor.id AS donor_id')
            ->innerJoin(
                '\App\Entity\Donor',
                'donor',
                Join::WITH,
                'd.name = donor.name'
            )

            ->addOrderBy('d.' . $args['sort'], $args['order'] === 'a' ? 'ASC' : 'DESC');

        if (is_numeric($args['limit']) && (int)$args['limit'] !== -1) {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults((int)$args['limit']);
        }
        return  $qb->getQuery()->getArrayResult();
    }

    public function getTabs($donation = false, $isAdmin = false)
    {
        if (!is_object($donation) || !$donation->getId()) {
            return [];
        }
        $out = [];
        foreach ($this->tabs as $idx => $data) {
            $route = $data[0];
            switch ($route) {
                default:
                    $out[] = $data;
                    break;
            }
        }
        return $out;
    }


}
