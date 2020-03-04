<?php

namespace App\Repository;

use App\Columns\ListenerLogs as ListenerLogsColumns;
use App\Columns\ListenerSignals as ListenerSignalsColumns;
use App\Entity\Listener;
use App\Utils\Rxx;
use App\Columns\Listeners as ListenersColumns;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class ListenerRepository extends ServiceEntityRepository
{
    private $connection;

    private $tabs = [
        ['listener', 'Profile'],
        ['listener_map', 'Map'],
        ['listener_locatormap', 'Locator Map'],
        ['listener_signals', 'Signals (%%signals%%)'],
        ['listener_logs', 'Logs (%%logs%%)'],
        ['listener_signalmap', 'Signals Map'],
        ['listener_export', 'Export'],
        ['listener_weather', 'Weather'],
        ['listener_stats', 'Stats'],
        ['listener_awards', 'Awards'],
    ];

    private $listenersColumns;
    private $listenerLogsColumns;
    private $listenerSignalsColumns;
    private $logRepository;
    private $regionRepository;

    public function __construct(
        Connection $connection,
        RegionRepository $regionRepository,
        ManagerRegistry $registry,
        ListenersColumns $listenersColumns,
        ListenerLogsColumns $listenerLogsColumns,
        ListenerSignalsColumns $listenerSignalsColumns,
        LogRepository $logRepository
    ) {
        parent::__construct($registry, Listener::class);
        $this->connection = $connection;
        $this->listenersColumns = $listenersColumns->getColumns();
        $this->listenerLogsColumns = $listenerLogsColumns->getColumns();
        $this->listenerSignalsColumns = $listenerSignalsColumns->getColumns();
        $this->logRepository = $logRepository;
        $this->regionRepository = $regionRepository;
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

    private function addFilterHasMapPos($qb, $args)
    {
        if (!isset($args['has_map_pos'])) {
            return;
        }
        switch ($args['has_map_pos']) {
            case 'N':
                $qb
                    ->andWhere('(l.mapX = 0 AND l.mapY = 0)')
                    ->andWhere('(l.region = :eu OR l.region = :na)')
                    ->andWhere('(l.itu != :azr AND l.itu != :svb)')
                    ->setParameter('eu', 'eu')
                    ->setParameter('na', 'na')
                    ->setParameter('azr', 'AZR')
                    ->setParameter('svb', 'SVB');
                break;
            case 'Y':
                $qb
                    ->andWhere('(l.mapX != 0 OR l.mapY != 0)')
                    ->andWhere('(l.region = :eu OR l.region = :na)')
                    ->andWhere('(l.itu != :azr AND l.itu != :svb)')
                    ->setParameter('eu', 'eu')
                    ->setParameter('na', 'na')
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
        if (empty($args['filter'])) {
            return;
        }
        $qb
            ->andWhere('(l.name like :filter or l.qth like :filter or l.callsign like :filter)')
            ->setParameter('filter', '%'.$args['filter'].'%');
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
                    ->andWhere('(l.region = :oc and l.itu = :hwa) or (l.region in (:na_ca))')
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }
    }

    public function getColumns()
    {
        return $this->listenersColumns;
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
                'logs.listenerid = l.id AND logs.signalid = :signalId'
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
            ->select('l.id, l.mapX, l.mapY, l.name, l.primaryQth, logs.heardIn, logs.dxMiles, logs.dxKm, MAX(logs.daytime) AS daytime')
            ->innerJoin('\App\Entity\Log', 'logs')
            ->andWhere('logs.listenerid = l.id')
            ->andWhere('logs.signalid = :signalId')
            ->setParameter('signalId', $signalId)
            ->andWhere('(l.mapX != 0 OR l.mapY != 0)')
            ->andWhere('l.countLogs != 0')
            ->addGroupBy('l.id')
            ->addOrderBy('logs.heardIn', 'ASC')
            ->addOrderBy('l.name', 'ASC');

        $this->addFilterMap($qb, $map);

        return $qb->getQuery()->execute();
    }

    public function getLogsColumns()
    {
        return $this->listenerLogsColumns;
    }

    public function getStats($system, $region)
    {
        $listeners =    $this->getFilteredListenersCount($system, [ 'region' => $region ]);
        if (!$listeners) {
            return [];
        }
        $loggings =     $this->logRepository->getFilteredLogsCount($system, $region);
        $dates =        $this->logRepository->getFirstAndLastLog($system, $region);

        $stats = [
            'Focus' =>        ($region ? $this->regionRepository->get($region)->getName() : ""),
            'Locations' =>    number_format($listeners),
            'Loggings' =>     number_format($loggings)
        ];
        if ($loggings) {
            $stats['First log'] = date('j M Y', strtotime($dates['first']));
            $stats['Last log'] = date('j M Y', strtotime($dates['last' ]));
        }
        return [ 'listeners' => $stats ];
    }

    public function getSignalsColumns()
    {
        return $this->listenerSignalsColumns;
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
                    ($row->getPrimaryQth() ? '*' : '---')
                    . " "
                    . $row->getName()
                    . ", "
                    . $row->getQth()
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

    public function getTabs($listener = false, $isAdmin = false)
    {
        if (!$listener->getId()) {
            return [];
        }
        $logs =     $listener->getCountLogs();
        $signals =  $listener->getCountSignals();
        $knownQth = ($listener->getLat() || $listener->getLon());
        $out = [];
        foreach ($this->tabs as $idx => $data) {
            $route = $data[0];
            switch ($route) {
                case 'listener_export':
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
                case 'listener_map':
                    if ($knownQth) {
                        $out[] = $data;
                    }
                    break;
                case 'listener_signalmap':
                    if ($listener->getSignalsMap()) {
                        $out[] = $data;
                    }
                    break;
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

    public function getFilteredListeners($system, $args)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('l');

        if (isset($args['sort']) && $this->listenersColumns[$args['sort']]['sort']) {
            $qb->addSelect(
                "(CASE WHEN (".$this->listenersColumns[$args['sort']]['sort'].") = '' THEN 1 ELSE 0 END) AS _blank"
            );
        }

        $this->addFilterSystem($qb, $system);
        $this->addFilterHasMapPos($qb, $args);
        $this->addFilterSearch($qb, $args);
        $this->addFilterCountry($qb, $args);
        $this->addFilterRegion($qb, $args);

        if (isset($args['show']) && $args['show'] === 'map') {
            $qb
                ->andWhere('(l.lat != 0 OR l.lon !=0)');
        }

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page']) && isset($args['show']) && $args['show'] !== 'map') {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && $this->listenersColumns[$args['sort']]['sort']) {
            if ($args['sort'] === 'name') {
                $qb
                    ->addOrderBy(
                        'l.name',
                        ($args['order'] == 'd' ? 'DESC' : 'ASC')
                    )
                    ->addOrderBy('l.primaryQth', 'DESC')
                    ->addOrderBy('l.sp', 'ASC')
                    ->addOrderBy('l.itu', 'ASC')
                    ->addOrderBy('l.qth', 'ASC');
            } else {
                $qb
                    ->addOrderBy(
                        '_blank',
                        'ASC'
                    )
                    ->addOrderBy(
                        ($this->listenersColumns[$args['sort']]['sort']),
                        ($args['order'] == 'd' ? 'DESC' : 'ASC')
                    );
            }
        }
        $result = $qb->getQuery()->execute();

        // Necessary to resolve extra nesting in results caused by extra select to ignore empty fields in sort order
        $out = [];
        foreach ($result as $key => $value) {
            $out[] = $value[0];
        }
        return $out;
    }

    public function getFilteredListenersCount($system, $args)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('COUNT(l.id) as count');

        $this->addFilterSystem($qb, $system);
        $this->addFilterHasMapPos($qb, $args);
        $this->addFilterSearch($qb, $args);
        $this->addFilterCountry($qb, $args);
        $this->addFilterRegion($qb, $args);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getLatestLoggedListeners($system, $limit = 25)
    {
        $fields =
             'l.id,'
            .'l.name,'
            .'l.sp,'
            .'l.itu';

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
            ->select('l.name');
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
            .'s.pwr,'
            .'s.lat,'
            .'s.lon,'
            .'(CASE WHEN s.sp = \'\' THEN s.itu ELSE s.sp END) as place,'
            .'trim(l.sec)+0 as sec,'
            .'(CASE WHEN trim(l.sec)+0 = 0 THEN \'\' ELSE trim(l.sec)+0 END) as secF,'
            .'CONCAT(l.lsbApprox,l.lsb) AS lsb,'
            .'CONCAT(l.usbApprox,l.usb) AS usb,'
            .'l.daytime,'
            .'l.format,'
            .'l.dxKm,'
            .'l.dxMiles';

        $qb = $this
            ->createQueryBuilder('li')
            ->select($columns)
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.listenerid = li.id')

            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalid = s.id')

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
            .'trim(s.sec)+0 AS sec,'
            .'(CASE WHEN trim(s.sec)+0 = 0 THEN \'\' ELSE trim(s.sec)+0 END) AS secF,'
            .'s.format,'
            .'s.gsq,'
            .'s.type,'
            .'s.lsb,'
            .'s.usb,'
            .'s.pwr,'
            .'s.lat,'
            .'s.lon,'
            .'s.notes,'
            .'l.dxKm,'
            .'l.dxMiles,'
            .'COUNT(l.signalid) AS logs,'
            .'MAX(l.daytime) AS daytime,'
            .'MIN(l.date) AS earliest,'
            .'MAX(l.date) AS latest';

        $qb = $this
            ->createQueryBuilder('li')
            ->select($columns)
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.listenerid = li.id')

            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalid = s.id')

            ->andWhere('li.id = :listenerID')
            ->setParameter('listenerID', $listenerID);

        if (isset($args['type']) && $args['type'] !== '') {
            $qb
                ->andWhere('s.type in(:type)')
                ->setParameter('type', $args['type']);
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
        foreach ($result as &$row) {
            $row['qth'] = str_replace("\"", "\\\"", html_entity_decode($row['qth']));
            $row['notes'] = str_replace("\"", "\\\"", html_entity_decode($row['notes']));
        }
        return $result;
    }

    public function getSignalTypesForListener($listenerID) {
        $qb = $this
            ->createQueryBuilder('li')
            ->select('s.type')
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.listenerid = li.id')

            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalid = s.id')

            ->andWhere('li.id = :listenerID')
            ->setParameter('listenerID', $listenerID)

            ->groupBy('s.type');

        $results = $qb->getQuery()->execute();
        $out = [];
        foreach ($results as $result) {
            $out[$result['type']] = $result['type'];
        }
        return $out;
    }
}
