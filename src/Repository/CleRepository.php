<?php

namespace App\Repository;

use App\Entity\Cle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CleRepository extends ServiceEntityRepository
{
    const AWARDSPEC = [
        'daytime' => [
            [ 250,   499,  402,  804 ],
            [ 500,   749,  805, 1205 ],
            [ 750,   999, 1206, 1607 ],
            [ 1000, 1249, 1608, 2010 ],
            [ 1250,    0, 1608,    0 ]
        ],
        'longranger' => [
            [ 500,   999,  805, 1607 ],
            [ 1000, 1499, 1608, 2413 ],
            [ 1500, 1999, 2414, 3217 ],
            [ 2000, 2499, 3218, 4022 ],
            [ 2500, 2999, 4023, 4826 ],
            [ 3000, 4999, 4827, 8044 ],
            [ 5000,    0, 8045,    0 ]
        ],
        'region_eu' => [ 10, 20, 30, 40 ],
        'region_na' => [ 10, 30, 45, 60 ],
        'region_ca' => [  3, 10, 15, 20 ],
        'region_sa' => [  1,  3,  5, 10 ],
        'region_af' => [  1,  5, 10, 15 ],
        'region_as' => [  1,  3,  5, 10 ],
        'region_oc' => [  1,  3,  5, 10 ],
        'region_an' => [  1 ],
        'country_benelux' => [
            'ITU' => [ 'BEL', 'HOL', 'LUX' ],
            'NUM' => [  5, 15, 25, 35 ]
        ],
        'country_france' => [
            'ITU' => [ 'FRA', 'COR' ],
            'NUM' => [ 20, 40, 80, 120 ]
        ],
        'country_germany' => [
            'ITU' => [ 'DEU' ],
            'NUM' => [ 20, 30, 60, 100 ]
        ],
        'country_scandanavia' => [
            'ITU' => [ 'SWE', 'NOR', 'FIN', 'DNK' ],
            'NUM' => [ 25, 100, 200, 300 ]
        ],
        'country_uk' => [
            'ITU' => [ 'ENG', 'WLS', 'IOM', 'GSY', 'JSY', 'SCT', 'ORK', 'SHE', 'NIR', 'IRL' ],
            'NUM' => [ 20, 40, 80, 120 ]
        ],
        'country_italy' => [
            'ITU' => [ 'ITA', 'SAR', 'SCY' ],
            'NUM' => [ 20, 40, 60, 90 ]
        ],
        'country_iberia' =>  [
            'ITU' => [ 'ESP', 'POR', 'BAL' ],
            'NUM' => [ 20, 40, 60, 90 ]
        ],
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cle::class);
    }

    public function getAwardSpec($type)
    {
        return static::AWARDSPEC[$type];
    }
}
