<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Maps
 *
 * @ORM\Table(name="maps")
 * @ORM\Entity
 */
class Map
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="SP", type="string", length=2, nullable=false, options={"fixed"=true})
     */
    private $sp = '';

    /**
     * @var int
     *
     * @ORM\Column(name="ix1", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $ix1 = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="ix2", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $ix2 = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="iy1", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $iy1 = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="iy2", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $iy2 = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="lon1", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lon1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lon2", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lon2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lat1", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lat1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lat2", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lat2;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $region = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ITU", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $itu = '';


}
