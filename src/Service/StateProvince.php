<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-06-11
 * Time: 07:49
 */

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
    protected $em;

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