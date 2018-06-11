<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sp as SpEntity;

/**
 * Class Region
 * @package App\Service
 */
class StateProvince
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Region constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getStates($countries = null)
    {
        $filter = ($countries ? ['itu' => explode(',', $countries)] : []);

        return
            $this->em
                ->getRepository(SpEntity::class)
                ->findBy($filter, ['name' => 'ASC']);
    }

}