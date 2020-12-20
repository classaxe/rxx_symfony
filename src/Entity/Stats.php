<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stats
 *
 * @ORM\Table(name="stats")
 * @ORM\Entity
 */
class Stats
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
     * @var int
     *
     * @ORM\Column(name="listeners_reu", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersReu = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rna", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRna = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRww = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_af", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwAf = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_an", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwAn = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_as", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwAs = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_ca", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwCa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_eu", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwEu = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_iw", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwIw = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_na", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwNa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_oc", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwOc = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="listeners_rww_sa", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $listenersRwwSa = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_reu", type="date", nullable=true)
     */
    private $logFirstReu;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rna", type="date", nullable=true)
     */
    private $logFirstRna;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww", type="date", nullable=true)
     */
    private $logFirstRww;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_af", type="date", nullable=true)
     */
    private $logFirstRwwAf;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_an", type="date", nullable=true)
     */
    private $logFirstRwwAn;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_as", type="date", nullable=true)
     */
    private $logFirstRwwAs;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_ca", type="date", nullable=true)
     */
    private $logFirstRwwCa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_eu", type="date", nullable=true)
     */
    private $logFirstRwwEu;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_iw", type="date", nullable=true)
     */
    private $logFirstRwwIw;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_na", type="date", nullable=true)
     */
    private $logFirstRwwNa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_oc", type="date", nullable=true)
     */
    private $logFirstRwwOc;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_first_rww_sa", type="date", nullable=true)
     */
    private $logFirstRwwSa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_reu", type="date", nullable=true)
     */
    private $logLastReu;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rna", type="date", nullable=true)
     */
    private $logLastRna;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww", type="date", nullable=true)
     */
    private $logLastRww;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_af", type="date", nullable=true)
     */
    private $logLastRwwAf;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_an", type="date", nullable=true)
     */
    private $logLastRwwAn;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_as", type="date", nullable=true)
     */
    private $logLastRwwAs;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_ca", type="date", nullable=true)
     */
    private $logLastRwwCa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_eu", type="date", nullable=true)
     */
    private $logLastRwwEu;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_iw", type="date", nullable=true)
     */
    private $logLastRwwIw;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_na", type="date", nullable=true)
     */
    private $logLastRwwNa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_oc", type="date", nullable=true)
     */
    private $logLastRwwOc;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="log_last_rww_sa", type="date", nullable=true)
     */
    private $logLastRwwSa;

    /**
     * @var int
     *
     * @ORM\Column(name="logs_reu", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsReu = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rna", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRna = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRww = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_af", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwAf = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_an", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwAn = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_as", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwAs = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_ca", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwCa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_eu", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwEu = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_iw", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwIw = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_na", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwNa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_oc", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwOc = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="logs_rww_sa", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $logsRwwSa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_reu", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsReu = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rna", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRna = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rna_reu", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRnaReu = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRww = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_af", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwAf = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_an", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwAn = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_as", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwAs = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_ca", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwCa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_eu", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwEu = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_iw", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwIw = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_na", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwNa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_oc", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwOc = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_rww_sa", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsRwwSa = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="signals_unlogged", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $signalsUnlogged = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    private $timestamp;


}
