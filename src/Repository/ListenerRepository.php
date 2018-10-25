<?php

namespace App\Repository;

use App\Columns\ListenerLogs as ListenerLogsColumns;
use App\Columns\ListenerSignals as ListenerSignalsColumns;
use App\Entity\Listener;
use App\Utils\Rxx;
use App\Columns\Listeners as ListenersColumns;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ListenerRepository extends ServiceEntityRepository
{
    private $tabs = [
        ['listener', 'Profile'],
        ['listener_signals', 'Signals (%%signals%%)'],
        ['listener_logs', 'Logs (%%logs%%)'],
        ['listener_export', 'Export'],
//        ['listener_weather', 'Weather'],
//        ['listener_stats', 'stats']
    ];

    private $listenersColumns;
    private $listenerLogsColumns;
    private $listenerSignalsColumns;

    public function __construct(
        RegistryInterface $registry,
        ListenersColumns $listenersColumns,
        ListenerLogsColumns $listenerLogsColumns,
        ListenerSignalsColumns $listenerSignalsColumns
    ) {
        parent::__construct($registry, Listener::class);
        $this->listenersColumns = $listenersColumns->getColumns();
        $this->listenerLogsColumns = $listenerLogsColumns->getColumns();
        $this->listenerSignalsColumns = $listenerSignalsColumns->getColumns();
    }

    public function getColumns()
    {
        return $this->listenersColumns;
    }

    public function getLogsColumns()
    {
        return $this->listenerLogsColumns;
    }

    public function getSignalsColumns()
    {
        return $this->listenerSignalsColumns;
    }

    public function getTabs($listener = false)
    {
        if (!$listener) {
            return [];
        }
        $out = [];
        foreach ($this->tabs as $route => $label) {
            $out[$route] = str_replace(
                ['%%signals%%', '%%logs%%'],
                [$listener->getCountSignals(), $listener->getCountLogs()],
                $label
            );
        }
        return $out;
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

    public function getFilteredListeners($system, $args)
    {
        $qb = $this->createQueryBuilder('l');
        if ($this->listenersColumns[$args['sort']]['sort']) {
            $qb
                ->addSelect(
                    "(CASE WHEN (".$this->listenersColumns[$args['sort']]['sort'].")='' THEN 1 ELSE 0 END) AS _blank"
                );
        }

        $this->addFilterSystem($qb, $system);

        if ($args['filter']) {
            $qb
                ->andWhere('(l.name like :filter or l.qth like :filter or l.callsign like :filter)')
                ->setParameter('filter', '%'.$args['filter'].'%');
        }
        if ($args['country']) {
            $qb
                ->andWhere('(l.itu = :country)')
                ->setParameter('country', $args['country']);
        }
        if (isset($args['region']) && $args['region']) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $args['region']);
        }
        if ($this->listenersColumns[$args['sort']]['sort']) {
            $qb
                ->orderBy(
                    '_blank',
                    'ASC'
                )
                ->addOrderBy(
                    ($this->listenersColumns[$args['sort']]['sort']),
                    ($args['order'] == 'd' ? 'DESC' : 'ASC')
                );
        }
        $result = $qb->getQuery()->execute();
        // Necessary to resolve extra nesting in results caused by extra select to ignore empty fields in sort order
        $out = [];
        foreach ($result as $key => $value) {
            $out[] = $value[0];
        }
        return $out;
    }

    public function getLatestLoggedListeners($system, $limit = 25)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('l.id')
            ->addSelect('l.name')
            ->addSelect('l.sp')
            ->addSelect('l.itu');
        $this->addFilterSystem($qb, $system);
        $qb
            ->setMaxResults($limit)
            ->orderBy('l.logLatest', 'DESC');

        $result = $qb
            ->getQuery()
            ->execute();
        uasort($result, array($this, 'cmpObj'));

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
            .'s.gsq,'
            .'s.type,'
            .'trim(l.sec)+0 as sec,'
            .'(CASE WHEN trim(l.sec)+0 = 0 then \'\' ELSE trim(l.sec)+0 END) as secF,'
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

        if (isset($args['limit']) && $args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }

        if ($this->listenerLogsColumns[$args['sort']]['sort']) {
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
            .'trim(s.khz)+0 as khz,'
            .'s.active,'
            .'s.call,'
            .'s.qth,'
            .'s.sp,'
            .'s.itu,'
            .'trim(s.sec)+0 as sec,'
            .'(CASE WHEN trim(s.sec)+0 = 0 then \'\' ELSE trim(s.sec)+0 END) as secF,'
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
            .'COUNT(l.signalid) as logs,'
            .'MAX(l.date) as latest';

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

        $qb
            ->groupBy('s.id');

        if (isset($args['limit']) && $args['limit'] !== -1 && isset($args['page'])) {
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
