<?php

namespace App\Repository;

use App\Columns\Donors as DonorsColumns;
use App\Entity\Donor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\Query\Expr\Join;
use App\Utils\Rxx;


/**
 * @method Donor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donor[]    findAll()
 * @method Donor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonorRepository extends ServiceEntityRepository
{
    private $connection;
    private $donorsColumns;
    
    private $tabs = [
        ['donor', 'Profile'],
        //       ['donor_donations', 'Donations (%%donations%%)'],
    ];

    /**
     * @param Connection $connection
     * @param ManagerRegistry $registry
     **/
    public function __construct(
        Connection $connection,
        ManagerRegistry $registry,
        DonorsColumns $donorsColumns
    ) {
        $this->connection = $connection;
        $this->donorsColumns = $donorsColumns;
        parent::__construct($registry, Donor::class);
    }

    public function getColumns()
    {
        return $this->donorsColumns->getColumns();
    }

    public function getCount()
    {
        return $this
            ->createQueryBuilder('d')
            ->select('count(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOptions()
    {
        $donors = $this
            ->createQueryBuilder('d')
            ->select('d.id, d.name, d.display, d.sp, d.itu')
            ->addOrderBy('d.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
        $out = [ '' => '' ];
        foreach ($donors as $r) {
            $out[$r['name'] . ' (' . $r['display'] . ')'] = $r['id'];
        }

        return $out;
    }

    public function getRecords($args)
    {
        $qb = $this
            ->createQueryBuilder('d')
            ->select('
                d.id,
                d.name,
                d.display,
                d.email,
                d.callsign,
                d.anonymous,
                d.itu,
                d.sp,
                d.notes,
                COUNT(don.id) AS donations,
                SUM(don.amount) AS total
            ')
            ->leftJoin(
                '\App\Entity\Donation',
                'don',
                Join::WITH,
                'd.id = don.donorID'
            )
            ->addGroupBy('d.id')
            ->addOrderBy('d.' . $args['sort'], $args['order'] === 'a' ? 'ASC' : 'DESC');

        if (is_numeric($args['limit']) && (int)$args['limit'] !== -1) {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults((int)$args['limit']);
        }
        return  $qb->getQuery()->getArrayResult();
    }

    public function getStats()
    {
        $qb = $this
            ->createQueryBuilder('d')
            ->select('
                COUNT(DISTINCT d.id) AS donors,
                COUNT(don.id) AS donations,
                ROUND(SUM(don.amount) / COUNT(DISTINCT d.id),2) AS average_donor,
                ROUND(SUM(don.amount) / COUNT(don.id), 2) AS average_donation,
                SUM(don.amount) AS total')
            ->leftJoin(
                '\App\Entity\Donation',
                'don',
                Join::WITH,
                'd.id = don.donorID'
            );
        // print Rxx::y($qb->getQuery()->getResult()[0]); die;
        return  $qb->getQuery()->getResult()[0];
    }

    public function getTabs($donor = false, $isAdmin = false)
    {
        if (!is_object($donor) || !$donor->getId()) {
            return [];
        }
        $donations =          $donor->getCountDonations();
        $out = [];
        foreach ($this->tabs as $idx => $data) {
            $route = $data[0];
            switch ($route) {
                case 'donor_donations':
                    if ($donations) {
                        $out[] = str_replace(
                            ['%%donations%%'],
                            [$donations],
                            $data
                        );
                    }
                    break;
                default:
                    $out[] = $data;
                    break;
            }
        }
        return $out;
    }

    public function getDonorDetails($name='')
    {
        $sql = <<< EOD
            SELECT
                donors.*,
                sum(amount)
            FROM
                donations
            LEFT JOIN donors ON
                donations.donorId = donors.id
            GROUP BY
                donations.donorId
            ORDER BY
                sum(amount) DESC
EOD;
        /** @var Doctrine\DBAL\Driver\Statement $stmt */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAllAssociative();
    }

// /**
    //  * @return Donor[] Returns an array of Donor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Donor
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
