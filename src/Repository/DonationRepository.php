<?php

namespace App\Repository;

use App\Columns\Donations;
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
            ->select('
                d.id AS id,
                d.date AS date,
                d.donorID,
                d.amount AS amount,
                d.message AS message,
                donor.anonymous AS anonymous,
                donor.name AS name,
                donor.display AS display,
                donor.callsign AS callsign,
                donor.sp AS sp,
                donor.itu AS itu,
                donor.notes AS notes,
                donor.id AS donor_id
            ')
            ->innerJoin(
                '\App\Entity\Donor',
                'donor',
                Join::WITH,
                'd.donorID = donor.id'
            )
            ->addOrderBy($args['sort'], $args['order'] === 'a' ? 'ASC' : 'DESC');

        if (is_numeric($args['limit']) && (int)$args['limit'] !== -1) {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults((int)$args['limit']);
        }
        return  $qb->getQuery()->getArrayResult();
    }

    public function getDonationsPublic()
    {
        $qb = $this
            ->createQueryBuilder('d')
            ->select('
                d.date,
                (CASE WHEN donor.anonymous = 1 THEN CONCAT(\'Donor #\', donor.id) ELSE donor.display END) as name,
                (CASE WHEN donor.anonymous = 1 THEN (CASE WHEN donor.callsign != \'\' THEN \'<i>(Hidden)</i>\' ELSE \'\' END) ELSE donor.callsign END) as callsign,
                donor.sp,
                donor.itu,
                d.amount,
                (CASE WHEN donor.anonymous = 1 THEN (CASE WHEN d.message != \'\' THEN \'<i>(Message is hidden)</i>\' ELSE \'\' END) ELSE d.message END) as message')
            ->innerJoin(
                '\App\Entity\Donor',
                'donor',
                Join::WITH,
                'd.donorID = donor.id'
            )
            ->addOrderBy('d.date DESC, name');
            return  $qb->getQuery()->getArrayResult();
    }

    public function getDonationsYear() {
        $ago = date("Y-m-d", strtotime("-1 year"));
        $qb = $this
            ->createQueryBuilder('d')
            ->select("SUM(d.amount)")
            ->where("d.date > '$ago'");
        return  $qb->getQuery()->getSingleScalarResult();


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
