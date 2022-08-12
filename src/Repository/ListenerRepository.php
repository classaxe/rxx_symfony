<?php

namespace App\Repository;

use App\Columns\ListenerLogs as ListenerLogsColumns;
use App\Columns\ListenerLogsessions as ListenerLogsessionsColumns;
use App\Columns\ListenerRemoteLogs as ListenerRemoteLogsColumns;
use App\Columns\ListenerRemoteLogsessions as ListenerRemoteLogsessionsColumns;
use App\Columns\ListenerSignals as ListenerSignalsColumns;
use App\Entity\Listener;
use App\Entity\LogSession;
use App\Utils\Rxx;
use App\Columns\Listeners as ListenersColumns;
use DateTime;
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
    private $optionsCache = [];

    private $tabs = [
        ['listener', 'Profile'],
        ['listener_logsupload', 'Up ⇪'],
        ['listener_logs', 'Logs (%%logs%%)'],
        ['listener_logsessions', 'Sessions (%%logsessions%%)'],
        ['listener_signals', 'Signals (%%signals%%)'],
        ['listener_signalsmap', 'Signals Map'],
        ['listener_remote_logs', 'Remote Logs (%%remotelogs%%)'],
        ['listener_remote_logsessions', 'Remote Sessions (%%remotelogsessions%%)'],
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
    private $listenerRemoteLogsColumns;
    private $listenerRemoteLogsessionsColumns;
    private $logRepository;
    private $logsessionRepository;
    private $regionRepository;

    /**
     * @param Connection $connection
     * @param RegionRepository $regionRepository
     * @param ManagerRegistry $registry
     * @param ListenersColumns $listenersColumns
     * @param ListenerLogsColumns $listenerLogsColumns
     * @param ListenerLogsessionsColumns $listenerLogsessionsColumns
     * @param ListenerRemoteLogsColumns $listenerRemoteLogsColumns
     * @param ListenerRemoteLogsessionsColumns $listenerRemoteLogsessionsColumns
     * @param ListenerSignalsColumns $listenerSignalsColumns
     * @param LogRepository $logRepository
     * @param LogsessionRepository $logsessionRepository
     */
    public function __construct(
        Connection $connection,
        RegionRepository $regionRepository,
        ManagerRegistry $registry,
        ListenersColumns $listenersColumns,
        ListenerLogsColumns $listenerLogsColumns,
        ListenerLogsessionsColumns $listenerLogsessionsColumns,
        ListenerRemoteLogsColumns $listenerRemoteLogsColumns,
        ListenerRemoteLogsessionsColumns $listenerRemoteLogsessionsColumns,
        ListenerSignalsColumns $listenerSignalsColumns,
        LogRepository $logRepository,
        LogsessionRepository $logsessionRepository
    ) {
        parent::__construct($registry, Listener::class);
        $this->connection = $connection;
        $this->listenersColumns = $listenersColumns->getColumns();
        $this->listenerLogsColumns = $listenerLogsColumns->getColumns();
        $this->listenerLogsessionsColumns = $listenerLogsessionsColumns->getColumns();
        $this->listenerRemoteLogsColumns = $listenerRemoteLogsColumns->getColumns();
        $this->listenerRemoteLogsessionsColumns = $listenerRemoteLogsessionsColumns->getColumns();
        $this->listenerSignalsColumns = $listenerSignalsColumns->getColumns();
        $this->logRepository = $logRepository;
        $this->logsessionRepository = $logsessionRepository;
        $this->regionRepository = $regionRepository;
    }

    public function getTabs($listener = false, $isAdmin = false)
    {
        if (!$listener->getId()) {
            return [];
        }
        $logs =                 $listener->getCountLogs();
        $remotelogs =           $listener->getCountRemoteLogs();
        $logsessions =          $listener->getCountLogsessions();
        $remotelogsessions =    $listener->getCountRemoteLogsessions();
        $signals =              $listener->getCountSignals();
        $knownQth =             ($listener->getLat() || $listener->getLon());
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
                case 'listener_remote_logs':
                    if ($remotelogs) {
                        $out[] = str_replace(
                            ['%%remotelogs%%'],
                            [$remotelogs],
                            $data
                        );
                    }
                    break;
                case 'listener_remote_logsessions':
                    if ($remotelogsessions) {
                        $out[] = str_replace(
                            ['%%remotelogsessions%%'],
                            [$remotelogsessions],
                            $data
                        );
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

    private function addFilterCountry(&$qb, $args)
    {
        if (empty($args['country'])) {
            return;
        }
        $qb
            ->andWhere('(l.itu = :country)')
            ->setParameter('country', $args['country']);
    }

    private function addFilterEquipment(&$qb, $args)
    {
        if (empty($args['equipment'])) {
            return;
        }
        $entries = explode(' ', $args['equipment']);
        $options = [];
        foreach($entries as $idx => $ee) {
            $options[] = 'l.equipment like :equipment_' . $idx;
        }
        $qb->andWhere('(' . implode(' AND ', $options) . ')');
        foreach($entries as $idx => $ee) {
            $qb->setParameter('equipment_'.$idx, '%' . $ee . '%');
        }
    }

    private function addFilterHasLogs(&$qb, $args)
    {
        if (!isset($args['has_logs'])) {
            return;
        }
        switch ($args['has_logs']) {
            case 'Y':
                $qb->andWhere('(l.countLogs != 0)');
                break;
            case 'N':
                $qb->andWhere('(l.countLogs = 0)');
                break;
        }
    }

    private function addFilterHasMapPos(&$qb, $args)
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

    private function addFilterLoctype(&$qb, $args)
    {
        if (!isset($args['loctype'])) {
            return;
        }
        switch ($args['loctype']) {
            case 'N':
                $qb->andWhere('(l.primaryQth = \'N\')');
                break;
            case 'Y':
                $qb->andWhere('(l.primaryQth = \'Y\')');
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

    private function addFilterMultiop(&$qb, $args)
    {
        if (!isset($args['multiop'])) {
            return;
        }
        switch ($args['multiop']) {
            case 'N':
                $qb->andWhere('(l.multiOperator = \'N\')');
                break;
            case 'Y':
                $qb->andWhere('(l.multiOperator = \'Y\')');
                break;
        }
    }

    private function addFilterNotes(&$qb, $args)
    {
        if (empty($args['notes'])) {
            return;
        }
        $entries = explode(' ', $args['notes']);
        $options = [];
        foreach($entries as $idx => $ee) {
            $options[] = 'l.notes like :notes_' . $idx;
        }
        $qb->andWhere('(' . implode(' AND ', $options) . ')');
        foreach($entries as $idx => $ee) {
            $qb->setParameter('notes_'.$idx, '%' . $ee . '%');
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

    private function addFilterRxxId(&$qb, $args)
    {
        if (empty($args['rxx_id'])) {
            return;
        }
        $in_arr = [];
        $rxx_id_arr = explode(' ', str_replace(',', ' ', $args['rxx_id']));
        foreach($rxx_id_arr as $idx => $value) {
            $qb->setParameter('rxx_id_' . $idx, $value);
            $in_arr[] = ':rxx_id_' . $idx;
        }
        $qb->andWhere('l.id IN(' . implode(',', $in_arr) . ')');
    }

    private function addFilterSearch(&$qb, $args)
    {
        if (empty($args['q'])) {
            return;
        }
        $qb
            ->andWhere('(l.name like :filter or l.qth like :filter or l.callsign like :filter)')
            ->setParameter('filter', '%' . $args['q'] . '%');
    }

    private function addFilterStatus(&$qb, $args)
    {
        switch ($args['status']) {
            case 'Y':
                $qb->andWhere('(l.active = \'Y\')');
                break;
            case 'N':
                $qb->andWhere('(l.active = \'N\')');
                break;
            case '30D':
                $recent = (new DateTime())->modify('-30 day')->format('Y-m-d');
                $qb->andWhere('(l.active = \'Y\')');
                $qb->andWhere('(l.logLatest >= \'' . $recent .'\')');
                break;
            case '3M':
                $recent = (new DateTime())->modify('-3 month')->format('Y-m-d');
                $qb->andWhere('(l.active = \'Y\')');
                $qb->andWhere('(l.logLatest >= \'' . $recent .'\')');
                break;
            case '6M':
                $recent = (new DateTime())->modify('-6 month')->format('Y-m-d');
                $qb->andWhere('(l.active = \'Y\')');
                $qb->andWhere('(l.logLatest >= \'' . $recent .'\')');
                break;
            case '1Y':
                $recent = (new DateTime())->modify('-1 year')->format('Y-m-d');
                $qb->andWhere('(l.active = \'Y\')');
                $qb->andWhere('(l.logLatest >= \'' . $recent .'\')');
                break;
            case '2Y':
                $recent = (new DateTime())->modify('-2 year')->format('Y-m-d');
                $qb->andWhere('(l.active = \'Y\')');
                $qb->andWhere('(l.logLatest >= \'' . $recent .'\')');
                break;
            case '5Y':
                $recent = (new DateTime())->modify('-5 year')->format('Y-m-d');
                $qb->andWhere('(l.active = \'Y\')');
                $qb->andWhere('(l.logLatest >= \'' . $recent .'\')');
                break;
        }
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
            case 'logs':
                return $this->listenerLogsColumns;
            case 'logsessions':
                return $this->listenerLogsessionsColumns;
            case 'remotelogs':
                return $this->listenerRemoteLogsColumns;
            case 'remotelogsessions':
                return $this->listenerRemoteLogsessionsColumns;
            case 'signals':
                return $this->listenerSignalsColumns;
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
            . ' [' . $id . '] '
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

    public function getListenerForWwsuKey($wwsu_key)
    {
        $qb = $this
            ->createQueryBuilder('l', 'l.id')
            ->select('l.name, l.qth, l.gsq, l.wwsu_enable, l.wwsu_perm_cycle, l.wwsu_perm_offsets')
            ->andWhere('l.wwsu_key = :wwsu_key')
            ->setParameter('wwsu_key', $wwsu_key)
            ->andWhere('l.id = :rxx_id')
            ->setParameter('rxx_id', explode('|', $wwsu_key)[0]);

        $result = $qb->getQuery()->execute();

        return ['key' => $wwsu_key, 'result' => $result[0]];
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
            ->addGroupBy('l.id, logs.id');

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
            ->addGroupBy('l.id, logs.dxKm, logs.dxMiles, logs.heardIn')
            ->addOrderBy('logs.heardIn', 'ASC')
            ->addOrderBy('l.name', 'ASC');

        $this->addFilterMap($qb, $map);

        return $qb->getQuery()->execute();
    }

    public function getAll($onlyOperators = false)
    {
        $multiOpClause = ($onlyOperators ? "WHERE multi_operator = 'N'" : '');

        $sql = <<< EOD
            SELECT
                REPLACE(
                    CONCAT_WS(
                        '|',
                        id,
                        active,
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
            $multiOpClause
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
        $system = 0,
        $region = 0,
        $placeholder = false,
        $simple = false
    ) {
        if (!isset($this->optionsCache[$system . '|'. $region])) {
            $qb =
                $this
                    ->createQueryBuilder('l')
                    ->select('l.id, l.name, l.qth, l.sp, l.itu, l.gsq, l.primaryQth, l.callsign')
                    ->addOrderBy('l.name', 'ASC')
                    ->addOrderBy('l.primaryQth', 'DESC');
            $this->addFilterSystem($qb, $system);
            if ($region) {
                $qb
                    ->andWhere('(l.region = :region)')
                    ->setParameter('region', $region);
            }
            $this->optionsCache[$system . '|' . $region] = $qb->getQuery()->execute();
        }

        $result = $this->optionsCache[$system . '|'. $region];
        if ($placeholder) {
            $out = [ $placeholder => '' ];
        }
        foreach ($result as $row) {
            if ($simple) {
                $out[
                    ($row['primaryQth'] === 'Y' ? '' : html_entity_decode('&nbsp; '))
                    . " "
                    . html_entity_decode($row['name'])
                    . ' [' . $row['id'] . '] '
                    . html_entity_decode($row['qth'])
                    . ($row['sp'] ? ' ' . $row['sp'] : '')
                    . " "
                    . $row['itu']
                    . ($row['gsq'] ? ' | ' . $row['gsq'] : '')
                ] = $row['id'];
            } else {
                $out[
                    Rxx::pad_dot(
                        ($row['primaryQth'] === 'Y' ? "" : ". ")
                        . $row['name']
                        . " [" . $row['id'] . "] "
                        . $row['qth']
                        . " "
                        . $row['callsign'],
                        55
                    )
                    . ($row['sp'] ? " " . $row['sp'] : "...")
                    . " "
                    . $row['itu']
                ] = $row['id'];
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
        $this->addFilterLoctype($qb, $args);
        $this->addFilterSearch($qb, $args);
        $this->addFilterCountry($qb, $args);
        $this->addFilterRegion($qb, $args);
        $this->addFilterTimezone($qb, $args);
        $this->addFilterStatus($qb, $args);
        $this->addFilterMultiop($qb, $args);
        $this->addFilterEquipment($qb, $args);
        $this->addFilterNotes($qb, $args);
        $this->addFilterRxxId($qb, $args);

        if (isset($args['show']) && $args['show'] === 'map') {
            $qb->andWhere('(l.lat != 0 OR l.lon !=0)');
            if (!in_array($args['sort'], ['name', 'qth', 'sp', 'itu'])) {
                // Then we're sorting by an unseen column - confusing!
                $args['sort'] = 'name';
                $args['order'] = 'a';
            }
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
        if ((int)$args['limit'] !== -1) {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults($args['limit']);
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
        $this->addFilterLoctype($qb, $args);
        $this->addFilterSearch($qb, $args);
        $this->addFilterCountry($qb, $args);
        $this->addFilterRegion($qb, $args);
        $this->addFilterTimezone($qb, $args);
        $this->addFilterStatus($qb, $args);
        $this->addFilterMultiop($qb, $args);
        $this->addFilterEquipment($qb, $args);
        $this->addFilterNotes($qb, $args);
        $this->addFilterRxxId($qb, $args);
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
    count_logs =                (SELECT COUNT(*) FROM logs WHERE logs.listenerId = l.id),
    count_logsessions =         (SELECT COUNT(*) FROM log_sessions WHERE log_sessions.listenerId = l.id),
    count_remote_logs =         (SELECT COUNT(*) FROM logs WHERE logs.operatorId = l.id),
    count_remote_logsessions =  (SELECT COUNT(*) FROM log_sessions WHERE log_sessions.operatorId = l.id),
    count_signals =             (SELECT COUNT(DISTINCT signalId) FROM logs WHERE logs.listenerId = l.id),
    count_NDB =                 (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 0 WHERE logs.listenerId = l.id),
    count_DGPS =                (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 1 WHERE logs.listenerId = l.id),
    count_TIME =                (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 2 WHERE logs.listenerId = l.id),
    count_NAVTEX =              (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 3 WHERE logs.listenerId = l.id),
    count_HAMBCN =              (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 4 WHERE logs.listenerId = l.id),
    count_OTHER =               (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 5 WHERE logs.listenerId = l.id),
    count_DSC =                 (SELECT COUNT(DISTINCT signalId) FROM logs INNER JOIN signals s ON logs.signalId = s.id AND s.type = 6 WHERE logs.listenerId = l.id),
    log_earliest =              (SELECT MIN(date) FROM logs WHERE logs.listenerId = l.id),
    log_latest =                (SELECT MAX(date) FROM logs WHERE logs.listenerId = l.id),
    logsession_latest =         (SELECT MAX(timestamp) FROM log_sessions WHERE log_sessions.listenerId = l.id)
EOT;
        if ($listenerId) {
            $sql .= "\nWHERE\n    l.id = $listenerId";
        }
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getAllInvalidLogSessions()
    {
        $sql = <<< EOD
            SELECT
                ID
            FROM
                log_sessions
            WHERE
                ID IN(
                    SELECT
                        ID
                    FROM
                        log_sessions
                    WHERE
                        (logs IS NULL) OR
                        (logs != (SELECT COUNT(*) FROM logs l2 where l2.logSessionID = log_sessions.ID)) OR
                        (signals IS NULL) OR
                        (signals != (SELECT COUNT(DISTINCT signalID) FROM logs l2 where l2.logSessionID = log_sessions.ID))
                )
            GROUP BY
                ID
            LIMIT 500
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchFirstColumn();
    }

    public function updateStats($id)
    {
        $sql = <<< EOD
            SELECT
                (SELECT MIN(CONCAT(`date`, ' ', INSERT(`time`,3,0,':'), ':00')) FROM `logs` WHERE `logSessionId`=:logSessionId) AS `setFirstLog`,
                (SELECT MAX(CONCAT(`date`, ' ', INSERT(`time`,3,0,':'), ':00')) FROM `logs` WHERE `logSessionId`=:logSessionId) AS `setLastLog`,
                (SELECT COUNT(*) FROM `logs` WHERE `logSessionId`=:logSessionId) AS `setLogs`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` INNER JOIN `signals` ON `logs`.`signalID` = `signals`.`ID` WHERE `logs`.`logSessionId`=:logSessionId AND `signals`.`type`=1) AS `setLogsDgps`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` INNER JOIN `signals` ON `logs`.`signalID` = `signals`.`ID` WHERE `logs`.`logSessionId`=:logSessionId AND `signals`.`type`=6) AS `setLogsDsc`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` INNER JOIN `signals` ON `logs`.`signalID` = `signals`.`ID` WHERE `logs`.`logSessionId`=:logSessionId AND `signals`.`type`=4) AS `setLogsHambcn`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` INNER JOIN `signals` ON `logs`.`signalID` = `signals`.`ID` WHERE `logs`.`logSessionId`=:logSessionId AND `signals`.`type`=3) AS `setLogsNavtex`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` INNER JOIN `signals` ON `logs`.`signalID` = `signals`.`ID` WHERE `logs`.`logSessionId`=:logSessionId AND `signals`.`type`=0) AS `setLogsNdb`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` INNER JOIN `signals` ON `logs`.`signalID` = `signals`.`ID` WHERE `logs`.`logSessionId`=:logSessionId AND `signals`.`type`=5) AS `setLogsOther`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` INNER JOIN `signals` ON `logs`.`signalID` = `signals`.`ID` WHERE `logs`.`logSessionId`=:logSessionId AND `signals`.`type`=2) AS `setLogsTime`,
                (SELECT COUNT(DISTINCT `signalID`) FROM `logs` WHERE `logSessionId`=:logSessionId) AS `setSignals`;
EOD;
        $params = ['logSessionId' => $id];
        $stmt = $this->connection->prepare($sql);

        $stmt->execute($params);
        $stats = $stmt->fetchAssociative();

        /** @var LogSession $logSession */
        $logSession = $this->logsessionRepository->find($id);
        $em = $this->logsessionRepository->getEntityManager();
        if ($stats['setFirstLog'] === null) {
            $listenerId = $logSession->getListenerId();
            $operatorId = $logSession->getOperatorId();
            $em->remove($logSession);
            $em->flush();
            $this->updateListenerStats($listenerId);
            if ($operatorId) {
                $this->updateListenerStats($operatorId);
            }
            return;
        }
        if (is_bool(DateTime::createFromFormat('Y-m-d H:i:s', $stats['setFirstLog']))) {
            die("<h1>Error</h1><p>Invalid timestamp for first log in session $id: ". $stats['setFirstLog'] . "</p>");
        }
        if (is_bool(DateTime::createFromFormat('Y-m-d H:i:s', $stats['setLastLog']))) {
            die("<h1>Error</h1><p>Invalid timestamp for last log in session $id: ". $stats['setLastLog'] . "</p>");
        }
        $logSession
            ->setFirstLog(DateTime::createFromFormat('Y-m-d H:i:s', $stats['setFirstLog']) ?? null)
            ->setLastLog(DateTime::createFromFormat('Y-m-d H:i:s', $stats['setLastLog']) ?? null)
            ->setLogs($stats['setLogs'])
            ->setLogsDgps($stats['setLogsDgps'])
            ->setLogsDsc($stats['setLogsDsc'])
            ->setLogsHambcn($stats['setLogsHambcn'])
            ->setLogsNavtex($stats['setLogsNavtex'])
            ->setLogsNdb($stats['setLogsNdb'])
            ->setLogsOther($stats['setLogsOther'])
            ->setLogsTime($stats['setLogsTime'])
            ->setSignals($stats['setSignals']);
        $em->flush();
    }

    public function updateAllInvalidLogSessions()
    {
        $ids = $this->getAllInvalidLogSessions();
        foreach ($ids as $id) {
            $this->updateStats($id);
        }
        return count($ids);
    }

}
