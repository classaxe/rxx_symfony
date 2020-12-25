<?php

namespace App\Repository;

use App\Columns\ListenerLogs as ListenerLogsColumns;
use App\Columns\ListenerLogsessions as ListenerLogsessionsColumns;
use App\Columns\ListenerSignals as ListenerSignalsColumns;
use App\Entity\Listener;
use App\Utils\Rxx;
use App\Columns\Listeners as ListenersColumns;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

class ListenerRepository extends ServiceEntityRepository
{
    const defaultLimit =    500;
    const defaultOrder =    'a';
    const defaultPage =     0;
    const defaultSorting =  'name';

    private $connection;

    private $tabs = [
        ['listener', 'Profile'],
        ['listener_signals', 'Signals (%%signals%%)'],
        ['listener_signalsmap', 'Signals Map'],
        ['listener_logs', 'Logs (%%logs%%)'],
        ['listener_logsessions', 'Log Sessions (%%logsessions%%)'],
        ['listener_logsupload', 'Upload'],
        ['listener_map', 'Map'],
        ['listener_locatormap', 'Locator'],
        ['listener_weather', 'Weather'],
        ['listener_stats', 'Stats'],
        ['listener_awards', 'Awards'],
    ];

    private $listenersColumns;
    private $listenerLogsColumns;
    private $listenerLogsessionsColumns;
    private $listenerSignalsColumns;
    private $logRepository;
    private $regionRepository;

    public function __construct(
        Connection $connection,
        RegionRepository $regionRepository,
        ManagerRegistry $registry,
        ListenersColumns $listenersColumns,
        ListenerLogsColumns $listenerLogsColumns,
        ListenerLogsessionsColumns $listenerLogsessionsColumns,
        ListenerSignalsColumns $listenerSignalsColumns,
        LogRepository $logRepository
    ) {
        parent::__construct($registry, Listener::class);
        $this->connection = $connection;
        $this->listenersColumns = $listenersColumns->getColumns();
        $this->listenerLogsColumns = $listenerLogsColumns->getColumns();
        $this->listenerLogsessionsColumns = $listenerLogsessionsColumns->getColumns();
        $this->listenerSignalsColumns = $listenerSignalsColumns->getColumns();
        $this->logRepository = $logRepository;
        $this->regionRepository = $regionRepository;
    }

    public function getTabs($listener = false, $isAdmin = false)
    {
        if (!$listener->getId()) {
            return [];
        }
        $logs =     $listener->getCountLogs();
        $logsessions =  $listener->getCountLogsessions();
        $signals =  $listener->getCountSignals();
        $knownQth = ($listener->getLat() || $listener->getLon());
        $out = [];
        foreach ($this->tabs as $idx => $data) {
            $route = $data[0];
            switch ($route) {
                case 'listener_awards':
                case 'listener_logs':
                case 'listener_signals':
                case 'listener_stats':
                    if ($logs) {
                        $out[] = str_replace(
                            ['%%logs%%', '%%signals%%'],
                            [$logs, $signals],
                            $data
                        );
                    }
                    break;
                case 'listener_logsessions':
                    if ($logsessions) {
                        $out[] = str_replace(
                            ['%%logsessions%%'],
                            [$logsessions],
                            $data
                        );
                    }
                    break;
                case 'listener_map':
                    if ($knownQth) {
                        $out[] = $data;
                    }
                    break;
                case 'listener_signalsmap':
                    if ($listener->getSignalsMap()) {
                        $out[] = $data;
                    }
                    break;
                case 'listener_logsupload':
                case 'listener_locatormap':
                    if ($isAdmin) {
                        $out[] = $data;
                    }
                    break;
                case 'listener_weather':
                    if ($knownQth) {
                        $out[] = $data;
                    }
                    break;
                default:
                    $out[] = $data;
                    break;
            }
        }
        return $out;
    }

    private function addFilterCountry($qb, $args)
    {
        if (empty($args['country'])) {
            return;
        }
        $qb
            ->andWhere('(l.itu = :country)')
            ->setParameter('country', $args['country']);
    }

    private function addFilterHasLogs($qb, $args)
    {
        if (!isset($args['has_logs'])) {
            return;
        }
        switch ($args['has_logs']) {
            case 'N':
                $qb->andWhere('(l.countLogs = 0)');
                break;
            case 'Y':
                $qb->andWhere('(l.countLogs != 0)');
                break;
        }
    }

    private function addFilterHasMapPos($qb, $args)
    {
        if (!isset($args['has_map_pos'])) {
            return;
        }
        switch ($args['has_map_pos']) {
            case 'N':
                $qb
                    ->andWhere('(l.mapX = 0 AND l.mapY = 0)')
                    ->andWhere('(l.region = :ca OR l.region = :eu OR l.region = :na OR l.itu = :hwa)')
                    ->andWhere('(l.itu != :azr AND l.itu != :svb)')
                    ->setParameter('ca', 'ca')
                    ->setParameter('eu', 'eu')
                    ->setParameter('na', 'na')
                    ->setParameter('hwa', 'HWA')
                    ->setParameter('azr', 'AZR')
                    ->setParameter('svb', 'SVB');
                break;
            case 'Y':
                $qb
                    ->andWhere('(l.mapX != 0 OR l.mapY != 0)')
                    ->andWhere('(l.region = :ca OR l.region = :eu OR l.region = :na OR l.itu = :hwa)')
                    ->andWhere('(l.itu != :azr AND l.itu != :svb)')
                    ->setParameter('ca', 'ca')
                    ->setParameter('eu', 'eu')
                    ->setParameter('na', 'na')
                    ->setParameter('hwa', 'HWA')
                    ->setParameter('azr', 'AZR')
                    ->setParameter('svb', 'SVB');
                break;
        }
    }

    private function addFilterMap(&$qb, $map)
    {
        switch ($map) {
            case "eu":
                $qb
                    ->andWhere('(l.region = :eu)')
                    ->setParameter('eu', 'eu');
                break;
            case "na":
                $qb
                    ->andWhere('(l.region = :oc and l.itu = :hwa) or (l.region in (:na_ca))')
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }
    }

    private function addFilterRegion(&$qb, $args)
    {
        if (empty($args['region'])) {
            return;
        }
        $qb
            ->andWhere('(l.region = :region)')
            ->setParameter('region', $args['region']);
    }

    private function addFilterSearch($qb, $args)
    {
        if (empty($args['q'])) {
            return;
        }
        $qb
            ->andWhere('(l.name like :filter or l.qth like :filter or l.callsign like :filter)')
            ->setParameter('filter', '%' . $args['q'] . '%');
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
                    ->andWhere('l.region in (:na_ca)')
                    ->setParameter('na_ca', ['na','ca']);
                break;
        }
    }

    private function addFilterTimezone(&$qb, $args)
    {
        if (!isset($args['timezone']) || 'ALL' === $args['timezone']) {
            return;
        }
        $qb
            ->andWhere('(l.timezone = :timezone)')
            ->setParameter('timezone', $args['timezone']);
    }

    /**
     * @param string $mode
     * @return false|array
     */
    public function getColumns($mode = '')
    {
        switch ($mode) {
            case 'listeners':
                return $this->listenersColumns;
                break;
            case 'logs':
                return $this->listenerLogsColumns;
                break;
            case 'logsessions':
                return $this->listenerLogsessionsColumns;
                break;
            case 'signals':
                return $this->listenerSignalsColumns;
                break;
        }
        return false;
    }

    public function getDaytimeHours($timezone)
    {
        return [
            'start' =>  str_pad((1000 + $timezone * -100 % 2400), 4, '0', STR_PAD_LEFT),
            'end' =>    str_pad((1400 + $timezone * -100 % 2400), 4, '0', STR_PAD_LEFT)
        ];
    }

    public function getDescription($id)
    {
        $l = $this->find($id);
        if (!$l instanceof Listener) {
            return false;
        }
        return
            $l->getName()
            . ($l->getQth() ? ', ' . $l->getQth() : '')
            . ($l->getSp() ? ' ' . $l->getSp() : '')
            . ($l->getItu() ? ' ' . $l->getItu() : '')
            . ($l->getGsq() ? ' | ' . $l->getGsq() : '');
    }

    public function getSignalListenersSpItus($map)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('l.sp, l.itu')
            ->andWhere('l.countLogs != 0')
            ->addGroupBy('l.sp, l.itu');

        $this->addFilterMap($qb, $map);

        $results = $qb->getQuery()->execute();
        $out = [];
        foreach ($results as $result) {
            $out[] = $result['sp'] ? $result['sp'] : $result['itu'];
        }
        return $out;
    }

    public function getSignalListenersMapCoords($map, $signalId)
    {
        $qb = $this
            ->createQueryBuilder('l', 'l.id')
            ->select('l.mapX, l.mapY, l.primaryQth, (CASE WHEN logs.id IS NULL THEN 0 ELSE 1 END) AS heard')
            ->leftJoin(
                '\App\Entity\Log',
                'logs',
                Join::WITH,
                'logs.listenerId = l.id AND logs.signalId = :signalId'
            )
            ->andWhere('(l.mapX != 0 OR l.mapY != 0)')
            ->andWhere('l.countLogs != 0')
            ->setParameter('signalId', $signalId)
            ->addGroupBy('l.id');

        $this->addFilterMap($qb, $map);

        return $qb->getQuery()->execute();
    }

    public function getSignalListenersMapDetails($map, $signalId)
    {
        $qb = $this
            ->createQueryBuilder('l', 'l.id')
            ->select(
                'l.id,'
                . 'l.mapX,'
                . 'l.mapY,'
                . 'l.name,'
                . 'l.primaryQth,'
                . 'l.sp,'
                . 'l.itu,'
                . 'logs.dxMiles,'
                . 'logs.dxKm,'
                . 'MAX(logs.daytime) AS daytime'
            )
            ->innerJoin('\App\Entity\Log', 'logs')
            ->andWhere('logs.listenerId = l.id')
            ->andWhere('logs.signalId = :signalId')
            ->setParameter('signalId', $signalId)
            ->andWhere('(l.mapX != 0 OR l.mapY != 0)')
            ->andWhere('l.countLogs != 0')
            ->addGroupBy('l.id')
            ->addOrderBy('logs.heardIn', 'ASC')
            ->addOrderBy('l.name', 'ASC');

        $this->addFilterMap($qb, $map);

        return $qb->getQuery()->execute();
    }

    public function getAll()
    {
        $sql = <<< EOD
            SELECT
                REPLACE(
                    CONCAT_WS(
                        '|',
                        id,
                        name,
                        callsign,
                        gsq,
                        primary_QTH,
                        qth,
                        sp,
                        itu,
                        timezone
                    ),
                    '"',
                    '&quot;'
                ) as s
            FROM            
                `listeners`
            ORDER BY
                name,
                primary_QTH DESC,
                itu,
                sp,
                qth;
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchFirstColumn();
    }

    public function getAllOptions(
        $system = false,
        $region = false,
        $placeholder = false,
        $simple = false
    ) {
        $qb =
            $this
                ->createQueryBuilder('l')
                ->select('l')
                ->addOrderBy('l.name', 'ASC')
                ->addOrderBy('l.primaryQth', 'DESC')
        ;
        $this->addFilterSystem($qb, $system);
        if ($region) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $region);
        }

        $result = $qb->getQuery()->execute();

        if ($placeholder) {
            $out = [ $placeholder => '' ];
        }
        foreach ($result as $row) {
            if ($simple) {
                $out[
                    ($row->getPrimaryQth() ? '' : html_entity_decode('&nbsp; '))
                    . " "
                    . html_entity_decode($row->getName())
                    . ", "
                    . html_entity_decode($row->getQth())
                    . ($row->getSp() ? " " . $row->getSp() : "")
                    . " "
                    . $row->getItu()
                    . ($row->getGsq() ? ' | ' . $row->getGsq() : '')
                ] = $row->getId();
            } else {
                $out[
                    Rxx::pad_dot(
                        ($row->getPrimaryQth() ? "" : ". ")
                        . $row->getName()
                        . ", "
                        . $row->getQth()
                        . " "
                        . $row->getCallsign(),
                        55
                    )
                    . ($row->getSp() ? " " . $row->getSp() : "...")
                    . " "
                    . $row->getItu()
                ] = $row->getId();
            }
        }
        return $out;
    }

    public function getFilteredListeners($system, $args)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('l');

        if (isset($args['sort']) && ($this->listenersColumns[$args['sort']]['sort'] ?? false)) {
            $qb->addSelect(
                "(CASE WHEN (".$this->listenersColumns[$args['sort']]['sort'].") = '' THEN 1 ELSE 0 END) AS _blank"
            );
        }

        $this->addFilterSystem($qb, $system);
        $this->addFilterHasLogs($qb, $args);
        $this->addFilterHasMapPos($qb, $args);
        $this->addFilterSearch($qb, $args);
        $this->addFilterCountry($qb, $args);
        $this->addFilterRegion($qb, $args);
        $this->addFilterTimezone($qb, $args);

        if (isset($args['show']) && $args['show'] === 'map') {
            $qb
                ->andWhere('(l.lat != 0 OR l.lon !=0)');
        }

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page']) && isset($args['show']) && $args['show'] !== 'map') {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && ($this->listenersColumns[$args['sort']]['sort'] ?? false)) {
            switch ($args['sort']) {
                case 'name':
                    $qb
                        ->addOrderBy(
                            'l.name',
                            ($args['order'] == 'd' ? 'DESC' : 'ASC')
                        )
                        ->addOrderBy('l.primaryQth', 'DESC')
                        ->addOrderBy('l.sp', 'ASC')
                        ->addOrderBy('l.itu', 'ASC')
                        ->addOrderBy('l.qth', 'ASC');
                    break;
                case 'timezone':
                    $qb
                        ->addOrderBy(
                            '_blank',
                            'ASC'
                        )
                        ->addOrderBy(
                            'CAST(l.timezone AS DECIMAL(4,2))',
                            ($args['order'] == 'd' ? 'DESC' : 'ASC')
                        );
                    break;
                default:
                    $qb
                        ->addOrderBy(
                            '_blank',
                            'ASC'
                        )
                        ->addOrderBy(
                            ($this->listenersColumns[$args['sort']]['sort']),
                            ($args['order'] == 'd' ? 'DESC' : 'ASC')
                        );
                    break;
            }
        }
        $result = $qb->getQuery()->execute();

        // Necessary to resolve extra nesting in results caused by extra select to ignore empty fields in sort order
        $out = [];
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $out[] = $value[0];
            }
        }
        return $out ? $out : $result;
    }

    public function getFilteredListenersCount($system, $args)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('COUNT(l.id) as count');

        $this->addFilterSystem($qb, $system);
        $this->addFilterHasLogs($qb, $args);
        $this->addFilterHasMapPos($qb, $args);
        $this->addFilterSearch($qb, $args);
        $this->addFilterCountry($qb, $args);
        $this->addFilterRegion($qb, $args);
        $this->addFilterTimezone($qb, $args);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getFirstAndLastLog($system, $region = '')
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('MIN(l.logEarliest) AS first, MAX(l.logLatest) AS last');

        $this->addFilterSystem($qb, $system);

        if ($region) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $region);
        }
//        print "<pre>" . print_r($qb->getQuery()->getSQL(), true) . "</pre>";
        return $qb->getQuery()->getArrayResult()[0];
    }

    public function getLatestLoggedListeners($system, $limit = 25)
    {
        $fields =
            'l.id,'
            . 'l.name,'
            . 'l.qth,'
            . 'l.sp,'
            . 'l.itu,'
            . 'l.gsq,'
            . 'l.primaryQth';

        $qb = $this
            ->createQueryBuilder('l')
            ->select($fields);
        $this->addFilterSystem($qb, $system);
        $qb
            ->setMaxResults($limit)
            ->orderBy('l.logLatest', 'DESC');
        $result = $qb
            ->getQuery()
            ->execute();
        uasort($result, [$this, 'cmpObj']);

        return $result;
    }

    public static function cmpObj($a, $b)
    {
        $al = strtolower($a['name']);
        $bl = strtolower($b['name']);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    private function getLatestLogsListenersForDate($system, $date)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('l.id, l.name, l.qth, l.sp, l.itu, l.gsq, l.primaryQth');
        $this->addFilterSystem($qb, $system);
        $qb
            ->andWhere('(l.logLatest = :date)')
            ->setParameter('date', $date)
            ->orderBy('l.name', 'ASC');

        return $qb
            ->getQuery()
            ->execute();
    }

    private function getLatestLogsDate($system)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('max(l.logLatest)');

        $this->addFilterSystem($qb, $system);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLatestLogs($system)
    {
        $date =             $this->getLatestLogsDate($system);
        $listeners =        $this->getLatestLogsListenersForDate($system, $date);
        return [
            'date' =>       $date,
            'listeners' =>  $listeners
        ];
    }

    public function getTotalListeners($system)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('count(l.id)');

        $this->addFilterSystem($qb, $system);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLogsForListener($listenerID, array $args)
    {
        $columns =
            'trim(l.date) as logDate,'
            .'trim(l.time) as logTime,'
            .'s.id,'
            .'trim(s.khz)+0 as khz,'
            .'s.active,'
            .'s.call,'
            .'s.qth,'
            .'s.sp,'
            .'s.itu,'
            .'s.region,'
            .'s.gsq,'
            .'s.type,'
            .'(CASE when s.pwr = 0 THEN \'\' ELSE s.pwr END) AS pwr,'
            .'s.lat,'
            .'s.lon,'
            .'(CASE WHEN s.sp = \'\' THEN s.itu ELSE s.sp END) as place,'
            .'trim(l.sec)+0 as sec,'
            .'(CASE WHEN trim(l.sec)+0 = 0 THEN \'\' ELSE trim(l.sec)+0 END) as secF,'
            .'CONCAT(l.lsbApprox,l.lsb) AS lsb,'
            .'CONCAT(l.usbApprox,l.usb) AS usb,'
            .'l.id AS log_id,'
            .'l.daytime,'
            .'l.format,'
            .'l.dxKm,'
            .'l.dxMiles';

        $qb = $this
            ->createQueryBuilder('li')
            ->select($columns)
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.listenerId = li.id')

            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalId = s.id')

            ->andWhere('li.id = :listenerID')
            ->setParameter('listenerID', $listenerID);

        if (isset($args['type']) && $args['type'] !== '') {
            $qb
                ->andWhere('s.type in(:type)')
                ->setParameter('type', $args['type']);
        }

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && $this->listenerLogsColumns[$args['sort']]['sort']) {
            $idx = $this->listenerLogsColumns[$args['sort']];
            $qb
                ->addOrderBy(
                    ($idx['sort']),
                    ($args['order'] == 'd' ? 'DESC' : 'ASC')
                );
            if (isset($idx['sort_2']) && isset($idx['order_2'])) {
                $qb
                    ->addOrderBy(
                        ($idx['sort_2']),
                        ($idx['order_2'] == 'd' ? 'DESC' : 'ASC')
                    );
            }
        }

        $result = $qb->getQuery()->execute();
//        print "<pre>".print_r($result, true)."</pre>";
        return $result;
    }

    public function getLogsessionsForListener($listenerID, array $args)
    {
        $columns =
             'ls.id,'
            .'trim(ls.timestamp) as timestamp,'
            .'u.name,'
            .'trim(ls.firstLog) as firstLog,'
            .'trim(ls.lastLog) as lastLog,'
            .'ls.logs';

        $qb = $this
            ->createQueryBuilder('li')
            ->select($columns)
            ->innerJoin('\App\Entity\Logsession', 'ls', 'WITH', 'ls.listenerId = li.id')
            ->innerJoin('\App\Entity\User', 'u', 'WITH', 'ls.administratorId = u.id')
            ->andWhere('li.id = :listenerID')
            ->setParameter('listenerID', $listenerID);

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && $this->listenerLogsessionsColumns[$args['sort']]['sort']) {
            $idx = $this->listenerLogsessionsColumns[$args['sort']];
            $qb
                ->addOrderBy(
                    ($idx['sort']),
                    ($args['order'] === 'd' ? 'DESC' : 'ASC')
                );
        }

        $result = $qb->getQuery()->execute();
//        print "<pre>".print_r($result, true)."</pre>";
        return $result;
    }

    public function getSignalCountsForListener($listenerID)
    {
        $qb = $this
            ->createQueryBuilder('li')
            ->select('COUNT(distinct s.id) as count')
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.listenerId = li.id')

            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalId = s.id')

            ->andWhere('li.id = :listenerID')
            ->setParameter('listenerID', $listenerID);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getSignalsForListener($listenerID, array $args = [])
    {
        $columns =
            's.id,'
            .'trim(s.khz)+0 AS khz,'
            .'s.active,'
            .'s.call,'
            .'s.qth,'
            .'s.sp,'
            .'s.itu,'
            .'s.region,'
            .'s.gsq,'
            .'s.type,'
            .'(CASE WHEN s.lsb = 0 THEN \'\' ELSE CONCAT(s.lsbApprox, s.lsb) END) AS lsb,'
            .'(CASE WHEN s.usb = 0 THEN \'\' ELSE CONCAT(s.usbApprox, s.usb) END) AS usb,'
            .'trim(s.sec)+0 AS sec,'
            .'(CASE WHEN trim(s.sec)+0 = 0 THEN \'\' ELSE trim(s.sec)+0 END) AS secF,'
            .'s.format,'
            .'(CASE WHEN s.pwr = 0 THEN \'\' ELSE s.pwr END) AS pwr,'
            .'s.lat,'
            .'s.lon,'
            .'s.notes,'
            .'s.heardIn,'
            .'l.dxKm,'
            .'l.dxMiles,'
            .'COUNT(l.signalId) AS logs,'
            .'MAX(l.daytime) AS daytime,'
            .'MIN(l.date) AS earliest,'
            .'MAX(l.date) AS latest';

        $qb = $this
            ->createQueryBuilder('li')
            ->select($columns)
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.listenerId = li.id')

            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalId = s.id')

            ->andWhere('li.id = :listenerID')
            ->setParameter('listenerID', $listenerID);

        if (isset($args['type']) && $args['type'] !== '') {
            $qb
                ->andWhere('s.type in(:type)')
                ->setParameter('type', $args['type']);
        }

        if (isset($args['latlon']) && $args['latlon'] === true) {
            $qb
                ->andWhere('(s.lat != 0 AND s.lon != 0)');
        }

        if (isset($args['active']) && $args['active'] !== '') {
            $qb
                ->andWhere('s.active = :active')
                ->setParameter('active', $args['active']);
        }

        $qb
            ->groupBy('s.id');

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && $this->listenerSignalsColumns[$args['sort']]['sort']) {
            $idx = $this->listenerSignalsColumns[$args['sort']];
            $qb
                ->addOrderBy(
                    ($idx['sort']),
                    ($idx['order'] == 'd' ? 'DESC' : 'ASC')
                );
            if (isset($idx['sort_2']) && isset($idx['order_2'])) {
                $qb
                    ->addOrderBy(
                        ($idx['sort_2']),
                        ($idx['order_2'] == 'd' ? 'DESC' : 'ASC')
                    );
            }
        }

        $result = $qb->getQuery()->execute();
        foreach ($result as &$row) {
            $row['qth'] = str_replace("\"", "\\\"", html_entity_decode($row['qth']));
            $row['notes'] = str_replace("\"", "\\\"", html_entity_decode($row['notes']));
        }
        return $result;
    }

    public function getAllStats()
    {
        $sql = <<< EOD
            SELECT
                'rww' AS `system`,
                li_1.region,
                COUNT(*) AS `count`,
                MIN(li_1.log_earliest) AS `first`,
                MAX(li_1.log_latest) AS `last`
            FROM
                listeners li_1
            GROUP BY
                li_1.region
            UNION SELECT
                'rna',
                '',
                COUNT(*),
                MIN(li_2.log_earliest),
                MAX(li_2.log_latest)
            FROM
                listeners li_2
            WHERE
                li_2.region IN ('na','ca');
EOD;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAllAssociative();
    }


    public function updateListenerStats($listenerId = false)
    {
        $sql = <<< EOT
UPDATE
	listeners l
SET    
    count_logs =        (SELECT COUNT(*) FROM logs WHERE logs.listenerId = l.id),
    count_logsessions = (SELECT COUNT(*) FROM log_sessions WHERE log_sessions.listenerId = l.id),
    count_signals =     (SELECT COUNT(DISTINCT signalId) FROM logs WHERE logs.listenerId = l.id),
    count_NDB =         (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 0 WHERE logs.listenerId = l.id),
    count_DGPS =        (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 1 WHERE logs.listenerId = l.id),
    count_TIME =        (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 2 WHERE logs.listenerId = l.id),
    count_NAVTEX =      (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 3 WHERE logs.listenerId = l.id),
    count_HAMBCN =      (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 4 WHERE logs.listenerId = l.id),
    count_OTHER =       (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 5 WHERE logs.listenerId = l.id),
    count_DSC =         (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 6 WHERE logs.listenerId = l.id),
    log_earliest =      (SELECT MIN(date) FROM logs WHERE logs.listenerId = l.id),
    log_latest =        (SELECT MAX(date) FROM logs WHERE logs.listenerId = l.id)
EOT;
        if ($listenerId) {
            $sql .= "\nWHERE\n    l.id = $listenerId";
        }
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
