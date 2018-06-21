<?php

namespace App\Repository;

use App\Entity\Listener;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ListenerRepository extends ServiceEntityRepository
{
    private $columns = [
        'name' => [
            'admin'     =>  false,
            'arg'       =>  '',
            'field'     =>  'name',
            'label'     =>  'Name',
            'order'     =>  'a',
            'sort'      =>  'l.name',
            'td_class'  =>  '{% if listener.PrimaryQth %}primaryQth{% else %}secondaryQth{% endif %}',
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
    ];

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Listener::class);
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getFilteredListeners($system, $args)
    {
        $qb = $this->createQueryBuilder('l');
        switch($system) {
            case "reu":
                $qb
                    ->andWhere('(l.region = :eu)')
                    ->setParameter('eu','eu');
                break;
            case "rna":
                $qb
                    ->andWhere('(l.region = :oc and l.itu = :hwa) or (l.region in (:na_ca))')
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }
        if ($args['filter']) {
            $qb
                ->andWhere('(l.name like :filter or l.qth like :filter or l.callsign like :filter)')
                ->setParameter('filter', '%'.$args['filter'].'%')
            ;
        }
        if ($args['country']) {
            $qb
                ->andWhere('(l.itu = :country)')
                ->setParameter('country', $args['country'])
            ;
        }
        if (isset($args['region']) && $args['region']) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $args['region'])
            ;
        }
        $qb
            ->orderBy(
                ($this->columns[$args['sort']]['sort']),
                ($args['order'] == 'd' ? 'DESC' : 'ASC')
            );
        return
            $qb
                ->getQuery()
                ->execute();
    }

    public function getTotalListeners($system)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('count(l.id)');
        switch($system) {
            case "reu":
                $qb
                    ->andWhere('l.region = :eu')
                    ->setParameter('eu','eu');
                break;
            case "rna":
                $qb
                    ->andWhere('(l.region = :oc and l.itu = :hwa) or (l.region in (:na_ca))')
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }
}
