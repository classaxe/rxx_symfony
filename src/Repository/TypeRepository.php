<?php

namespace App\Repository;

use App\Entity\Itu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TypeRepository extends ServiceEntityRepository
{
    const types = [
        'DGPS' =>   'type_DGPS',
        'DSC' =>    'type_DSC',
        'Ham' =>    'type_HAMBCN',
        'Navtex' => 'type_NAVTEX',
        'NDB' =>    'type_NDB',
        'Time' =>   'type_TIME',
        'Other' =>  'type_OTHER',
        '(All)' =>  'type_ALL'
    ];

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Itu::class);
    }

    public function getAllTypes()
    {
        return self::types;
    }

}
