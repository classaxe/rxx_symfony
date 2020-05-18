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

    const SWING_LF = 0.6;
    const SWING_HF = 1.5;

    const TOKENS = [
        'SINGLE' => [
            'KHZ', 'ID', 'GSQ', 'PWR', 'QTH', 'SP', 'ITU', 'sec', 'ITU-SP', 'x', 'X'
        ],

        'OFFSETS' => [
            'fmt', 'LSB', 'USB', '~LSB', '~USB', '+SB-', '+~SB-', '+K-', 'ABS', '~ABS'
        ],

        'TIME' => [
            'hh:mm', 'hhmm'
        ],

        'DATE_SINGLE' => [
            'D',   'DD',  'M',    'MM',   'MMM',   'YY',    'YYYY'
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

    private function _checkLogDateTokens(&$tokens, &$logHas) {
        $logHas = [
            'partial' =>    false,
            'YYYY' =>       false,
            'MM' =>         false,
            'DD' =>         false
        ];
        foreach (static::TOKENS['YYYYMMDD'] as $token) {
            if (isset($tokens[$token])) {
                $tokens[$token]['type'] = 'date';
                $logHas['YYYY'] = true;
                $logHas['MM'] =   true;
                $logHas['DD'] =   true;
                return;
            }
        }
        foreach (static::TOKENS['MMDD'] as $token) {
            if (isset($tokens[$token])) {
                $tokens[$token]['type'] = 'date';
                $logHas['MM'] =   true;
                $logHas['DD'] =   true;
                break;
            }
        }
        foreach (static::TOKENS['DATE_SINGLE'] as $token) {
            if (isset($tokens[$token])) {
                $tokens[$token]['type'] = 'date';
                if (in_array($token, [ 'YYYY', 'YY' ])) {
                    $logHas['YYYY'] = true;
                }
                if (in_array($token, [ 'MMM', 'MM', 'M' ])) {
                    $logHas['MM'] = true;
                }
                if (in_array($token, [ 'DD', 'D' ])) {
                    $logHas['DD'] = true;
                }
            }
        }
        if (!$logHas['YYYY'] || !$logHas['MM'] || !$logHas['DD']) {
            $logHas['partial'] =   true;
        }
    }

    private function _checkLogOffsetTokens(&$tokens) {
        foreach (static::TOKENS['OFFSETS'] as $token) {
            if (isset($tokens[$token])) {
                $tokens[$token]['type'] = 'offsets';
            }
        }
    }

    private function _checkLogTimeTokens(&$tokens) {
        foreach (static::TOKENS['TIME'] as $token) {
            if (isset($tokens[$token])) {
                $tokens[$token]['type'] = 'time';
                return;
            }
        }
    }

    private function _extractDate($token, $value, $YYYY, $MM, $DD)
    {
        if (false !==($idx = strpos($token, 'YYYY'))) {
            $_YYYY = substr($value, $idx, 4);
        } elseif (false !== ($idx = strpos($token, 'YY'))) {
            $x = substr($value, $idx, 2);
            $_YYYY = (int)$x < 70 ? "20$x" : "19$x";
        } else {
            $_YYYY = $YYYY;
        }

        if (false !== ($idx = strpos($token, 'MMM'))) {
            $_MM = RXX::convertMMMtoMM(substr($value, $idx, 3));
        } elseif (false !== ($idx = strpos($token, 'MM'))) {
            $_MM = substr($value, $idx, 2);;
        } elseif (false !== ($idx = strpos($token, 'M'))) {
            $x = substr($value, $idx, 1);
            $_MM = (strlen($x)<2 ? '0' : '') . $x;
        } else {
            $_MM = $MM;
        }

        if (false !== ($idx = strpos($token, 'DD'))) {
            $_DD = substr($value, $idx, 2);
        } elseif (false !== ($idx = strpos($token, 'D'))) {
            $x = substr($value, $idx, 1);
            $_DD = (strlen($x)<2 ? '0' : '') . $x;
        } else {
            $_DD = $DD;
        }

        return [
            'YYYY' =>   $_YYYY,
            'MM' =>     $_MM,
            'DD' =>     $_DD
        ];
    }

    private function _extractFormatAndOffsets($token, $value, $KHZ) {
        $out = [
            'fmt' =>        '',
            'LSB' =>        '',
            'LSB_approx' => '',
            'USB' =>        '',
            'USB_approx' => '',
        ];
        switch ($token) {
            case 'LSB':
            case 'USB':
                $out[$token] = $value;
                if (substr($value, 0, 1) === '~') {
                    $out[$token. '_approx'] = '~';
                    $out[$token] = substr($value, 1);
                }
                if ($value === '---') {
                    // Andy Robins logs use --- as blank
                    $out['$token'] = '';
                }
                break;

            case '~LSB':
            case '~USB':
                $token = substr($token, 1);
                $out[$token. '_approx'] = '~';
                $out[$token] = $value;
                break;

            case '+SB-':
            case '+~SB-':
                // Convert hyphen symbol to - (For Steve R's Offsets)
                $value = str_replace('–', '-', $value);
                if ($token === '+~SB-') {
                    $value =    str_replace('~', '', $value); // Remove ~ symbol now we know it's approx
                }
                $sb_arr =    explode(' ', $value);
                for ($j = 0; $j < count($sb_arr); $j++) {
                    $sb =    trim($sb_arr[$j]);
                    if (in_array($sb, ['X', 'X-'])) {
                        // Format used by Jim Smith to indicate sb not present
                        $sb='';
                    }
                    if (in_array($sb, ['DAID', 'DA2ID', 'DA3ID', 'DBID', 'DB2ID', 'DB3ID'])) {
                        $out['fmt'] = $sb;
                    }
                    if ((substr($sb, 0, 1) === '+' && substr($sb, strlen($sb)-1, 1) === '-') ||
                        (substr($sb, 0, 1) === '-' && substr($sb, strlen($sb)-1, 1) === '+')
                    ) {
                        $out['USB'] = abs($sb);
                        $out['LSB'] = $out['USB'];
                    } elseif (substr($sb, 0, 1) === '±') {
                        $out['USB'] = abs(substr($sb, 1));
                        $out['LSB'] = $out['USB'];
                    } elseif (substr($sb, 0, 3) === '+/-' || substr($sb, 0, 3) === '-/+') {
                        $out['USB'] = abs(substr($sb, 3));
                        $out['LSB'] = $out['USB'];
                    } elseif (substr($sb, 0, 2) === '+-' || substr($sb, 0, 2) === '-+') {
                        $out['USB'] = abs(substr($sb, 2));
                        $out['LSB'] = $out['USB'];
                    } else {
                        $approx =    '';
                        if (substr($sb, 0, 1) === '~') {
                            $approx = '~';
                            $sb = substr($sb, 1);
                        }
                        if (substr($sb, 0, 1) === '+' || substr($sb, strlen($sb)-1, 1) === '+') {
                            // + at start or end
                            $out['USB'] = abs($sb);
                            $out['USB_approx'] =    $approx;
                        } elseif (substr($sb, 0, 1)=="-" || substr($sb, strlen($sb)-1, 1) === '-') {
                            // - at start or end
                            $out['LSB'] = abs($sb);
                            $out['LSB_approx'] =    $approx;
                        } elseif (substr($sb, 0, 1) === '±') {
                            $out['USB'] = abs(substr($sb, 1));
                            $out['LSB'] = $out['USB'];
                        } elseif (is_numeric($sb)) {
                            $out['USB'] = $sb;
                            // neither + nor -, therefore USB
                            $out['USB_approx'] =    $approx;
                        }
                    }
                    if ($token === '+~SB-') {
                        $out['USB_approx'] =    "~";
                        $out['LSB_approx'] =    "~";
                    }
                }
                break;

            case '+K-':
                // Cope with Brian Keyte's +0.4 1- offsets
                $value = str_replace('–', '-', $value);
                if ($value === '0.4') {
                    $out['USB_approx'] =    '~';
                    $out['USB'] =           '400';
                    $out['LSB_approx'] =    '~';
                    $out['LSB'] =           '400';
                } elseif ($value === '+0.4') {
                    $out['USB_approx'] =    '~';
                    $out['USB'] =           '400';
                } elseif ($value === '-0.4') {
                    $out['LSB_approx'] =    '~';
                    $out['LSB'] =           '400';
                } elseif ($value === '1') {
                    $out['USB_approx'] =    '~';
                    $out['USB'] =           '1020';
                    $out['LSB_approx'] =    '~';
                    $out['LSB'] =           '1020';
                } elseif ($value === '+1') {
                    $out['USB_approx'] =    '~';
                    $out['USB'] =           '1020';
                } elseif ($value === '-1') {
                    $out['LSB_approx'] =    '~';
                    $out['LSB'] =           '1020';
                }
                break;

            case 'ABS':
            case '~ABS':
                $ABS_arr =    explode(" ", $value);
                for ($j=0; $j<count($ABS_arr); $j++) {
                    // print "ABS=$value, KHZ=$this->KHZ";
                    $ABS = (double)trim($ABS_arr[$j]);
                    if ($ABS) {
                        if ($ABS >(float)$KHZ) {
                            $out['USB'] = round((1000 * ($ABS - $KHZ)));
                        } else {
                            $out['LSB'] = round((1000 * ($KHZ - $ABS)));
                        }
                        if ($token === '~ABS') {
                            $out['USB_approx'] =    "~";
                            $out['LSB_approx'] =    "~";
                        }
                    }
                }
                break;

        }
        return $out;
    }

    private function _extractTime($token, $value) {
        if ($token === 'hhmm') {
            return (is_numeric($value) ? $value : 'ERROR');
        }
        $x = explode(':', $value);
        if (2 !== count($x)) {
            return 'ERROR';
        }
        return str_pad($x[0], 2, '0', STR_PAD_LEFT)
            . str_pad($x[1], 2, '0', STR_PAD_LEFT);
    }

    public function parseFormat($format, &$tokens, &$errors, &$logHas) {
        $valid = array_merge(
            static::TOKENS['SINGLE'],
            static::TOKENS['OFFSETS'],
            static::TOKENS['TIME'],
            static::TOKENS['DATE_SINGLE'],
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
                    $tokens[$key] = [
                        'start' =>  $start,
                        'len' =>    $len,
                        'type' =>   ''
                    ];
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
        $this->_checkLogDateTokens($tokens, $logHas);
        $this->_checkLogTimeTokens($tokens);
        $this->_checkLogOffsetTokens($tokens);
    }

    public function parseLog($listener, $logs, $tokens, $YYYY, $MM, $DD, $signalRepository) {
        $lines = [];
        $log_lines = explode("\r", str_replace("\r\n", "\r", stripslashes($logs)));
        foreach($log_lines as $line) {
            $data = [
                'date' => [],
                'offsets' => [
                    'LSB' => '',
                    'LSB_approx' => '',
                    'USB' => '',
                    'USB_approx' => ''
                ],
                'time' => '',
                'daytime' => false,
                'fmt' => '',
                'sec' => '',
                'QTH' => '',
                'GSQ' => '',
                'LSB' => '',
                'LSB_approx' => '',
                'USB' => '',
                'USB_approx' => '',
                'ITU' => '',
                'SP' => '',
                'pwr' => ''
            ];
            foreach ($tokens as $token => $spec) {
                if (in_array($token, ['x', 'X'])) {
                    continue;
                }
                $value = trim(substr($line, $spec['start'], $spec['len']));
                if ($token === 'sec') {
                    $value = str_replace(',', '.', $value);
                }
                switch($spec['type']) {
                    case 'date':
                        $result = $this->_extractDate($token, $value, $YYYY, $MM, $DD);
                        foreach ($result as $idx => $v) {
                            if (!$v) {
                                continue;
                            }
                            $data['date'][$idx] = $v;
                        }
                        break;
                    case 'time':
                        $data['time'] = $this->_extractTime($token, $value);
                        $data['daytime'] = $listener->isDaytime($data['time']);
                        break;
                    case 'offsets':
                        $result = $this->_extractFormatAndOffsets($token, $value, $data['KHZ']);
                        foreach ($result as $idx => $v) {
                            if (!$v) {
                                continue;
                            }
                            if ($idx === 'fmt' && $v) {
                                $data['fmt'] = $v;
                                continue;
                            }
                            $data['offsets'][$idx] = $v;
                        }
                        break;
                    default:
                        $data[$token] = $value;
                        break;
                }
            }
            if (!$data['ID'] || !$data['date']) {
                continue;
            }

            // Combine date fields:
            $d = $data['date'];
            $data['YYYYMMDD'] = "{$d['YYYY']}-{$d['MM']}-{$d['DD']}";
            unset($data['date']);

            // Flatten Offset fields:
            $keys = array_keys($data['offsets']);
            foreach ($keys as $key) {
                $data[$key] = $data['offsets'][$key];
            }
            unset($data['offsets']);

            // Separate ITU and SP from ITU-SP:
            if (isset($data['ITU-SP'])) {
                $x = explode('-', $data['ITU-SP']);
                $data['ITU'] =  $x[0];
                $data['SP'] =   $x[1] ?? '';
                unset($data['ITU-SP']);
            }
            $lines[] = $data;
        }

        foreach ($lines as &$line) {
            $line['options'] = $signalRepository->getSignalCandidates($line['ID'], $line['KHZ'], $listener);
        }
        return $lines;
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
