<?php

namespace App\Repository;

use App\Entity\Listener;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ListenerRepository extends ServiceEntityRepository
{
    private $listenersColumns = [
        'name' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'name',
            'label'     =>  'Name',
            'order'     =>  'a',
            'sort'      =>  'l.name',
            'td_class'  =>  '(handled by twig)',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'addlog' => [
            'admin'     =>  true,
            'arg'       =>  '',
            'field'     =>  'formattedAddlogLink',
            'label'     =>  'Log',
            'order'     =>  '',
            'sort'      =>  '',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'email' => [
            'admin'     =>  true,
            'arg'       =>  '',
            'field'     =>  'email',
            'label'     =>  'Email',
            'order'     =>  'a',
            'sort'      =>  'l.email',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'callsign' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'formattedCallsignLink',
            'label'     =>  'Callsign',
            'order'     =>  'a',
            'sort'      =>  'l.callsign',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Ham Radio Callsign',
        ],
        'qth' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'qth',
            'label'     =>  'QTH',
            'order'     =>  'a',
            'sort'      =>  'l.qth',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Location',
        ],
        'sp' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'sp',
            'label'     =>  'S/P',
            'order'     =>  'a',
            'sort'      =>  'l.sp',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'State / Province / Territory',
        ],
        'itu' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'itu',
            'label'     =>  'ITU',
            'order'     =>  'a',
            'sort'      =>  'l.itu',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Country',
        ],
        'region' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'formattedRegion',
            'label'     =>  '<div>Continent</div>',
            'order'     =>  'a',
            'sort'      =>  'l.region',
            'td_class'  =>  '',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  '',
        ],
        'gsq' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'gsq',
            'label'     =>  'GSQ',
            'order'     =>  'a',
            'sort'      =>  'l.gsq',
            'td_class'  =>  'txt_r monspace',
            'th_class'  =>  '',
            'tooltip'   =>  'Listener Grid Square',
        ],
        'timezone' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'timezone',
            'label'     =>  '<div>Timezone</div>',
            'order'     =>  'a',
            'sort'      =>  'l.timezone',
            'td_class'  =>  'txt_r',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Timezone (relative to UTC)',
        ],
        'countLogs' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'formattedCountLogs',
            'label'     =>  '<div>Total Logs</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countLogs',
            'td_class'  =>  'txt_r',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Total number of logs for all signals',
        ],
        'logLatest' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'formattedLogLatest',
            'label'     =>  'Latest Log',
            'order'     =>  'd',
            'sort'      =>  'l.logLatest',
            'td_class'  =>  'text-nowrap txt_r monspace',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'countDgps' => [
            'admin'     =>  false,
            'arg'       =>  'type_DGPS',
            'field'     =>  'formattedCountDgps',
            'label'     =>  '<div>DGPS</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countDgps',
            'td_class'  =>  'txt_r type_dgps',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Number of DGPS stations received',
        ],
        'countDsc' => [
            'admin'     =>  false,
            'arg'       =>  'type_DSC',
            'field'     =>  'formattedCountDsc',
            'label'     =>  '<div>DSC</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countDsc',
            'td_class'  =>  'txt_r type_dsc',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Number of DSC stations received',
        ],
        'countHambcn' => [
            'admin'     =>  false,
            'arg'       =>  'type_HAMBCN',
            'field'     =>  'formattedCountHambcn',
            'label'     =>  '<div>HAMBCN</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countHambcn',
            'td_class'  =>  'txt_r type_hambcn',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Number of Ham Beacons received',
        ],
        'countNavtex' => [
            'admin'     =>  false,
            'arg'       =>  'type_NAVTEX',
            'field'     =>  'formattedCountNavtex',
            'label'     =>  '<div>NAVTEX</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countNavtex',
            'td_class'  =>  'txt_r type_navtex',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Number of NAVTEX stations received',
        ],
        'countNdb' => [
            'admin'     =>  false,
            'arg'       =>  'type_NDB',
            'field'     =>  'formattedCountNdb',
            'label'     =>  '<div>NDB</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countNdb',
            'td_class'  =>  'txt_r type_ndb',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Number of NDBs received',
        ],
        'countTime' => [
            'admin'     =>  false,
            'arg'       =>  'type_TIME',
            'field'     =>  'formattedCountTime',
            'label'     =>  '<div>TIME</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countTime',
            'td_class'  =>  'txt_r type_time',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Number of Time stations received',
        ],
        'countOther' => [
            'admin'     =>  false,
            'arg'       =>  'type_OTHER',
            'field'     =>  'formattedCountOther',
            'label'     =>  '<div>OTHER</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countOther',
            'td_class'  =>  'txt_r type_other',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Number of Other signals received',
        ],
        'countSignals' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'formattedCountSignals',
            'label'     =>  '<div>All Signals</div>',
            'order'     =>  'd',
            'sort'      =>  'l.countSignals',
            'td_class'  =>  'txt_r',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  'Total number of signals of all types',
        ],
        'website' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'formattedWebsiteLink',
            'label'     =>  'WWW',
            'sortBy'    =>  [['l.wn','DESC'],['l.website', 'DIR']],
            'order'     =>  'a',
            'sort'      =>  'l.website',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'nwl' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'formattedNdbWeblogLink',
            'label'     =>  'NWL',
            'order'     =>  '',
            'sort'      =>  '',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'NDB Weblog for Listener',
        ],
        'mapPos' => [
            'admin'     =>  true,
            'arg'       =>  '',
            'field'     =>  'formattedMapPos',
            'label'     =>  '<div>Map Pos</div>',
            'order'     =>  'a',
            'sort'      =>  'l.mapX',
            'td_class'  =>  '',
            'th_class'  =>  'txt_vertical',
            'tooltip'   =>  '',
        ],
        'admin' => [
            'admin'     =>  true,
            'arg'       =>  '',
            'field'     =>  'formattedDeleteLink',
            'label'     =>  'Admin',
            'order'     =>  '',
            'sort'      =>  '',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
    ];

    private $listenerLogsColumns = [
        'logDate' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'logDate',
            'label'     =>  'Date',
            'order'     =>  'a',
            'sort'      =>  'logDate',
            'sort_2'    =>  'logTime',
            'order_2'   =>  'a',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'logTime' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'logTime',
            'label'     =>  'Time',
            'order'     =>  'a',
            'sort'      =>  'logTime',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'khz' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'khz',
            'label'     =>  'KHz',
            'order'     =>  'a',
            'sort'      =>  's.khz',
            'sort_2'    =>  's.call',
            'order_2'   =>  'a',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'call' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'call',
            'label'     =>  'ID',
            'order'     =>  'a',
            'sort'      =>  's.call',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'qth' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'qth',
            'label'     =>  'Location',
            'order'     =>  'a',
            'sort'      =>  's.qth',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'sp' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'sp',
            'label'     =>  'S/P',
            'order'     =>  'a',
            'sort'      =>  's.sp',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'State / Province',
        ],
        'itu' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'itu',
            'label'     =>  'ITU',
            'order'     =>  'a',
            'sort'      =>  's.itu',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'ITU Country Code',
        ],
        'gsq' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'gsq',
            'label'     =>  'GSQ',
            'order'     =>  'a',
            'sort'      =>  's.gsq',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Maidenhead Locator Grid Square',
        ],
        'sec' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'secF',
            'label'     =>  'Sec.',
            'order'     =>  'a',
            'sort'      =>  'l.sec',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Cycle Time in seconds',
        ],
        'format' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'format',
            'label'     =>  'Fmt.',
            'order'     =>  'a',
            'sort'      =>  'l.format',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Format of signal',
        ],
        'dxKm' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'dxKm',
            'label'     =>  'KM',
            'order'     =>  'd',
            'sort'      =>  'l.dxKm',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'dxMiles' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'dxMiles',
            'label'     =>  'Miles',
            'order'     =>  'd',
            'sort'      =>  'l.dxMiles',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
    ];

    private $listenerSignalsColumns = [
        'khz' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'khz',
            'label'     =>  'KHz',
            'order'     =>  'a',
            'sort'      =>  's.khz',
            'sort_2'    =>  's.call',
            'order_2'   =>  'a',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'call' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'call',
            'label'     =>  'ID',
            'order'     =>  'a',
            'sort'      =>  's.call',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'qth' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'qth',
            'label'     =>  'Location',
            'order'     =>  'a',
            'sort'      =>  's.qth',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'sp' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'sp',
            'label'     =>  'S/P',
            'order'     =>  'a',
            'sort'      =>  's.sp',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'State / Province',
        ],
        'itu' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'itu',
            'label'     =>  'ITU',
            'order'     =>  'a',
            'sort'      =>  's.itu',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'ITU Country Code',
        ],
        'gsq' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'gsq',
            'label'     =>  'GSQ',
            'order'     =>  'a',
            'sort'      =>  's.gsq',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Maidenhead Locator Grid Square',
        ],
        'sec' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'secF',
            'label'     =>  'Sec.',
            'order'     =>  'a',
            'sort'      =>  'sec',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Cycle Time in seconds',
        ],
        'format' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'format',
            'label'     =>  'Fmt.',
            'order'     =>  'a',
            'sort'      =>  's.format',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  'Format of signal',
        ],
        'logs' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'logs',
            'label'     =>  'Logs',
            'order'     =>  'a',
            'sort'      =>  's.logs',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'latest' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'latest',
            'label'     =>  'Latest',
            'order'     =>  'd',
            'sort'      =>  'latest',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'dxKm' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'dxKm',
            'label'     =>  'KM',
            'order'     =>  'd',
            'sort'      =>  'dxKm',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
        'dxMiles' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'dxMiles',
            'label'     =>  'Miles',
            'order'     =>  'd',
            'sort'      =>  'dxMiles',
            'td_class'  =>  '',
            'th_class'  =>  '',
            'tooltip'   =>  '',
        ],
    ];

    private $menu_options = [
        ['listener', 'Profile'],
        ['listener_signals', 'Signals (%%signals%%)'],
        ['listener_logs', 'Logs (%%logs%%)'],
//        ['listener_export', 'Export'],
//        ['listener_weather', 'Weather']
    ];

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Listener::class);
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

    public function getMenuOptions($listener = false)
    {
        if (!$listener) {
            return [];
        }
        $out = [];
        foreach ($this->menu_options as $route => $label) {
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

    public function getSignalsForListener($listenerID, array $args)
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
            ->setParameter('listenerID', $listenerID)

            ->groupBy('s.id');

        if ($this->listenerSignalsColumns[$args['sort']]['sort']) {
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
//        print "<pre>".print_r($result, true)."</pre>";
        return $result;
    }
}
