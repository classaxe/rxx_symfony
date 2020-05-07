<?php

namespace App\Repository;

use App\Entity\Log;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

class LogRepository extends ServiceEntityRepository
{
    private $connection;

    const TOKENS = [
        'SINGLE' => [
            'D',   'DD',  'M',    'MM',   'MMM',   'YY',    'YYYY',   'hh:mm', 'hhmm',
            'KHZ', 'ID',  'GSQ',  'PWR',  'QTH',   'SP',    'ITU',    'sec',   'fmt',
            'LSB', 'USB', '~LSB', '~USB', '+SB-',  '+~SB-', '+K-',    'ABS',   '~ABS',
            'x',   'X'
        ],

        'MMDD' => [
            'DM',        'D.M',         'DDM',       'DD.M',
            'DMM',       'D.MM',        'DDMM',      'DD.MM',
            'DMMM',      'D.MMM',       'DDMMM',     'DD.MMM',

            'MD',        'M.D',         'MDD',       'M.DD',
            'MMD',       'MM.D',        'MMDD',      'MM.DD',
            'MMMD',      'MMM.D',       'MMMDD',     'MMM.DD'
        ],

        'YYYYMMDD' => [
            'DDMMYY',    'DD.MM.YY',    'DDYYMM',    'DD.YY.MM',
            'DDMMMYY',   'DD.MMM.YY',   'DDYYMMM',   'DD.YY.MMM',
            'DDMMYYYY',  'DD.MM.YYYY',  'DDYYYYMM',  'DD.YYYY.MM',
            'DDMMMYYYY', 'DD.MMM.YYYY', 'DDYYYYMMM', 'DD.YYYY.MMM',

            'MMDDYY',    'MM.DD.YY',    'MMYYDD',    'MM.YY.DD',
            'MMMDDYY',   'MMM.DD.YY',   'MMMYYDD',   'MMM.YY.DD',
            'MMDDYYYY',  'MM.DD.YYYY',  'MMYYYYDD',  'MM.YYYY.DD',
            'MMMDDYYYY', 'MMM.DD.YYYY', 'MMMYYYYDD', 'MMM.YYYY.DD',

            'YYDDMM',    'YY.DD.MM',    'YYMMDD',    'YY.MM.DD',
            'YYDDMMM',   'YY.DD.MMM',   'YYMMMDD',   'YY.MMM.DD',
            'YYYYDDMM',  'YYYY.DD.MM',  'YYYYMMDD',  'YYYY.MM.DD',
            'YYYYDDMMM', 'YYYY.DD.MMM', 'YYYYMMMDD', 'YYYY.MMM.DD'
        ]
    ];

    /**
     * LogRepository constructor.
     * @param ManagerRegistry $registry
     * @param Connection $connection
     */
    public function __construct(
        ManagerRegistry $registry,
        Connection $connection
    ) {
        parent::__construct($registry, Log::class);
        $this->connection = $connection;
    }

    public function getFilteredLogsCount($system, $region = '')
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('COUNT(l.id) as count');

        $this->addFilterSystem($qb, $system);

        if ($region) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $region);
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getFirstAndLastLog($system, $region = '')
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('MIN(l.date) AS first, MAX(l.date) AS last');

        $this->addFilterSystem($qb, $system);

        if ($region) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $region);
        }
        return $qb->getQuery()->getArrayResult()[0];
    }

    private function addFilterSystem(&$qb, $system)
    {
        switch ($system) {
            case "reu":
                $qb
                    ->andWhere('(l.region = :eu)')
                    ->setParameter('eu', 'eu');
                break;
            case "rna":
                $qb
                    ->andWhere('(l.region = :na)')
                    ->setParameter('na', 'na');
                break;
        }
    }

    private function getLogWhereClause($listenerId = false, $signalId = false)
    {
        return ($listenerId || $signalId ?
            "WHERE\n"
            . ($listenerId ? "    `logs`.`listenerID` = $listenerId" : '')
            . ($listenerId && $signalId ? " AND\n" : "\n")
            . ($signalId ? "    `logs`.`signalID` = $signalId" : '')
            : ''
        );
    }

    public function getLogCount($listenerId = false, $signalId = false)
    {
        $where = $this->getLogWhereClause($listenerId, $signalId);
        $sql = <<< EOD
            SELECT
                count(*)
            FROM
                `logs`
            INNER JOIN `signals` `s` ON
                `logs`.`signalID` = `s`.`ID`
            INNER JOIN `listeners` `l` ON
                `logs`.`listenerID` = `l`.`ID`
            $where
EOD;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getLogsForListener($listenerID)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select(
                'l.date,'
                . 'l.time,'
                . 'l.daytime,'
                . 'l.dxKm,'
                . 'l.dxMiles,'
                . 'CONCAT(l.lsbApprox, l.lsb) AS lsb,'
                . 'CONCAT(l.usbApprox, l.usb) AS usb,'
                . 'l.sec,'
                . 'l.format,'
                . 's.khz,'
                . 's.call,'
                . 's.type,'
                . 's.active,'
                . '(CASE WHEN s.pwr = 0 THEN \'\' ELSE s.pwr END) AS pwr,'
                . 's.sp,'
                . 's.itu,'
                . 's.region,'
                . 's.gsq,'
                . 's.lat,'
                . 's.lon,'
                . 's.qth'
            )
            ->andWhere('l.listenerid = :listenerID')
            ->setParameter('listenerID', $listenerID)
            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalid = s.id')
            ->orderBy(
                'l.date',
                'ASC'
            )
            ->addOrderBy(
                'l.time',
                'ASC'
            );
        $result = $qb->getQuery()->execute();
        foreach ($result as &$row) {
            $row['qth'] = str_replace("\"", "\\\"", html_entity_decode($row['qth']));
        }
        return $result;
    }

    public function checkLogDateTokens($tokens) {
        $result = [
            'partial' =>    false,
            'YYYY' =>       false,
            'MM' =>         false,
            'DD' =>         false
        ];
        foreach (static::TOKENS['YYYYMMDD'] as $token) {
            if (isset($tokens[$token])) {
                $result['YYYY'] = true;
                $result['MM'] =   true;
                $result['DD'] =   true;
                return $result;
            }
        }
        foreach (static::TOKENS['MMDD'] as $token) {
            if (isset($tokens[$token])) {
                $result['MM'] =   true;
                $result['DD'] =   true;
                break;
            }
        }
        if (isset($tokens["YYYY"]) || isset($tokens["YY"])) {
            $result['YYYY'] = true;
        }
        if (isset($tokens["MMM"]) || isset($tokens["MM"]) || isset($tokens["M"])) {
            $result['MM'] =   true;
        }
        if (isset($tokens["DD"]) || isset($tokens["D"])) {
            $result['DD'] =   true;
        }
        if (!$result['YYYY'] || !$result['MM'] || !$result['DD']) {
            $result['partial'] =   true;
        }
        return $result;
    }

    public function parseFormat($format) {
        $valid = array_merge(
            static::TOKENS['SINGLE'],
            static::TOKENS['MMDD'],
            static::TOKENS['YYYYMMDD']
        );
        $log_format_parse = $format . ' ';
        $start = 0;
        while (substr($log_format_parse, $start, 1) === ' ') {
            $start++;
        }
        $errors = [];
        $tokens = [];
        while ($start < strlen($log_format_parse)) {
            $len =  strpos(substr($log_format_parse, $start), ' ');
            $key =  substr($log_format_parse, $start, $len);
            if ($len) {
                while (substr($log_format_parse, $start + $len, 1) === ' ') {
                    $len++;
                }
                if ($key === 'X' || !isset($tokens[$key])) {
                    $tokens[$key] = [ $start, $len + 1 ];
                    if (!in_array($key, $valid)) {
                        $errors[$key] = [
                            'class' =>  'unknown',
                            'msg' =>    'Token not recognised'
                        ];
                    }
                } else {
                    $errors[$key] = [
                        'class' =>  'duplicate',
                        'msg' =>    'Token occurs more than once'
                    ];
                }
            }
            $start += $len;
        }
        return [ $tokens, $errors ];
    }

    public function updateDx($listenerId = false, $signalId = false)
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $chunksize = 200000;
        $affected = 0;

        $count = $this->getLogCount($listenerId, $signalId);
        $where = $this->getLogWhereClause($listenerId, $signalId);

        for ($offset = 0; $offset < $count; $offset += $chunksize) {
            $sql = <<< EOD
                SELECT
                    `logs`.`ID`,
                    `logs`.`dx_km`,
                    `logs`.`dx_miles`,
                    `s`.`lat` AS `s_lat`,
                    `s`.`lon` AS `s_lon`,
                    `l`.`lat` AS `l_lat`,
                    `l`.`lon` AS `l_lon`
                FROM
                    `logs`
                INNER JOIN `signals` `s` ON
                    `logs`.`signalID` = `s`.`ID`
                INNER JOIN `listeners` `l` ON
                    `logs`.`listenerID` = `l`.`ID`
                $where
                LIMIT $chunksize OFFSET $offset
EOD;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $r) {
                $dx = Rxx::getDx($r['l_lat'], $r['l_lon'], $r['s_lat'], $r['s_lon']);
                if (!$dx) {
                    continue;
                }
                if (($dx['km'] === (int)$r['dx_km']) && ($dx['miles'] === (int)$r['dx_miles'])) {
                    continue;
                }
                $sql = <<< EOD
                    UPDATE
                        `logs`
                    SET
                        `dx_km` = {$dx['km']},
                        `dx_miles` = {$dx['miles']}
                    WHERE
                        `ID` = {$r['ID']};
EOD;
                $stmt = $this->connection->prepare($sql);
                $stmt->execute();
                $affected += $stmt->rowCount();
            }
        }
        return $affected;
    }

    public function updateDaytime()
    {
        $sql = <<< EOD
        UPDATE
            logs
        INNER JOIN listeners l ON
            logs.listenerID = l.ID
        SET
            daytime = IF(
                (logs.time + 2400 >= 3400 + (l.timezone * -100)) AND
                (logs.time + 2400 < 3800 + (l.timezone * -100)),
                1, 0
            )
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
