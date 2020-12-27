<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-09-03
 * Time: 04:44
 */

namespace App\Columns;

class Listeners
{
    public function getColumns()
    {
        return [
            'name' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'name',
                'highlight' =>  'q',
                'label' =>      'Name',
                'order' =>      'a',
                'sort' =>       'l.name',
                'td_class' =>   'rowspan2',
                'th_class' =>   'rowspan2',
                'tooltip' =>    '',
            ],
            'callsign' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'callsign',
                'highlight' =>  'q',
                'label' =>      'Callsign',
                'order' =>      'a',
                'sort' =>       'l.callsign',
                'td_class' =>   'l2',
                'th_class' =>   'l2',
                'tooltip' =>    'Ham Radio Callsign',
            ],
            'qth' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'qth',
                'highlight' =>  'q',
                'label' =>      'QTH',
                'order' =>      'a',
                'sort' =>       'l.qth',
                'td_class' =>   '',
                'th_class' =>   '',
                'tooltip' =>    'Location',
            ],
            'sp' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'sp',
                'highlight' =>  false,
                'label' =>      'S/P',
                'order' =>      'a',
                'sort' =>       'l.sp',
                'td_class' =>   '',
                'th_class' =>   '',
                'tooltip' =>    'State / Province / Territory',
            ],
            'itu' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'itu',
                'highlight' =>  'country',
                'label' =>      'ITU',
                'order' =>      'a',
                'sort' =>       'l.itu',
                'td_class' =>   '',
                'th_class' =>   '',
                'tooltip' =>    'Country',
            ],
            'region' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'region',
                'highlight' =>  'region',
                'label' =>      'Region',
                'order' =>      'a',
                'sort' =>       'l.region',
                'td_class' =>   'text-uppercase',
                'th_class' =>   'txt_vertical',
                'tooltip' =>    '',
            ],
            'gsq' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'gsq',
                'highlight' =>  false,
                'label' =>      'GSQ',
                'order' =>      'a',
                'sort' =>       'l.gsq',
                'td_class' =>   'txt_r monospace',
                'th_class' =>   '',
                'tooltip' =>    'Listener Grid Square',
            ],
            'timezone' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'timezone',
                'highlight' =>  false,
                'label' =>      'Timezone',
                'order' =>      'a',
                'sort' =>       'l.timezone',
                'td_class' =>   'txt_r',
                'th_class' =>   'txt_vertical',
                'tooltip' =>    'Timezone (relative to UTC)',
            ],
            'logEarliest' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'formattedLogEarliest',
                'highlight' =>  false,
                'label' =>      'Earliest Log',
                'order' =>      'a',
                'sort' =>       'l.logEarliest',
                'td_class' =>   'text-nowrap txt_r monospace l2',
                'th_class' =>   'l2',
                'tooltip' =>    '',
            ],
            'logLatest' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'formattedLogLatest',
                'highlight' =>  false,
                'label' =>      'Latest Log',
                'order' =>      'd',
                'sort' =>       'l.logLatest',
                'td_class' =>   'text-nowrap txt_r monospace l2',
                'th_class' =>   'l2',
                'tooltip' =>    '',
            ],
            'logSessionLatest' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'formattedLogSessionLatest',
                'highlight' =>  false,
                'label' =>      'Latest Upload',
                'order' =>      'd',
                'sort' =>       'l.logSessionLatest',
                'td_class' =>   'text-nowrap txt_r monospace l2',
                'th_class' =>   'l2',
                'tooltip' =>    '',
            ],
            'countLogs' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'countLogs',
                'highlight' =>  false,
                'label' =>      'Total Logs',
                'order' =>      'd',
                'sort' =>       'l.countLogs',
                'td_class' =>   'txt_r',
                'th_class' =>   'txt_vertical',
                'tooltip' =>    'Total number of logs for all signals',
            ],
            'countDgps' => [
                'admin' =>      false,
                'arg' =>        'DGPS',
                'field' =>      'countDgps',
                'highlight' =>  false,
                'label' =>      'DGPS',
                'order' =>      'd',
                'sort' =>       'l.countDgps',
                'td_class' =>   'txt_r type_dgps l2',
                'th_class' =>   'txt_vertical l2',
                'tooltip' =>    'Number of DGPS stations received',
            ],
            'countDsc' => [
                'admin' =>      false,
                'arg' =>        'DSC',
                'field' =>      'countDsc',
                'highlight' =>  false,
                'label' =>      'DSC',
                'order' =>      'd',
                'sort' =>       'l.countDsc',
                'td_class' =>   'txt_r type_dsc l2',
                'th_class' =>   'txt_vertical l2',
                'tooltip' =>    'Number of DSC stations received',
            ],
            'countHambcn' => [
                'admin' =>      false,
                'arg' =>        'HAMBCN',
                'field' =>      'countHambcn',
                'highlight' =>  false,
                'label' =>      'HAMBCN',
                'order' =>      'd',
                'sort' =>       'l.countHambcn',
                'td_class' =>   'txt_r type_hambcn l2',
                'th_class' =>   'txt_vertical l2',
                'tooltip' =>    'Number of Ham Beacons received',
            ],
            'countNavtex' => [
                'admin' =>      false,
                'arg' =>        'NAVTEX',
                'field' =>      'countNavtex',
                'highlight' =>  false,
                'label' =>      'NAVTEX',
                'order' =>      'd',
                'sort' =>       'l.countNavtex',
                'td_class' =>   'txt_r type_navtex l2',
                'th_class' =>   'txt_vertical l2',
                'tooltip' =>    'Number of NAVTEX stations received',
            ],
            'countNdb' => [
                'admin' =>      false,
                'arg' =>        'NDB',
                'field' =>      'countNdb',
                'highlight' =>  false,
                'label' =>      'NDB',
                'order' =>      'd',
                'sort' =>       'l.countNdb',
                'td_class' =>   'txt_r type_ndb l2',
                'th_class' =>   'txt_vertical l2',
                'tooltip' =>    'Number of NDBs received',
            ],
            'countTime' => [
                'admin' =>      false,
                'arg' =>        'TIME',
                'field' =>      'countTime',
                'highlight' =>  false,
                'label' =>      'TIME',
                'order' =>      'd',
                'sort' =>       'l.countTime',
                'td_class' =>   'txt_r type_time l2',
                'th_class' =>   'txt_vertical l2',
                'tooltip' =>    'Number of Time stations received',
            ],
            'countOther' => [
                'admin' =>      false,
                'arg' =>        'OTHER',
                'field' =>      'countOther',
                'highlight' =>  false,
                'label' =>      'OTHER',
                'order' =>      'd',
                'sort' =>       'l.countOther',
                'td_class' =>   'txt_r type_other l2',
                'th_class' =>   'txt_vertical l2',
                'tooltip' =>    'Number of Other signals received',
            ],
            'countSignals' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'countSignals',
                'highlight' =>  false,
                'label' =>      'All Signals',
                'order' =>      'd',
                'sort' =>       'l.countSignals',
                'td_class' =>   'txt_r',
                'th_class' =>   'txt_vertical',
                'tooltip' =>    'Total number of signals of all types',
            ],
            'email' => [
                'admin' =>      true,
                'arg' =>        '',
                'field' =>      'email',
                'highlight' =>  false,
                'label' =>      'Email',
                'order' =>      'a',
                'sort' =>       'l.email',
                'td_class' =>   'l2',
                'th_class' =>   'l2',
                'tooltip' =>    '',
            ],
            'website' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'website',
                'highlight' =>  false,
                'label' =>      'WWW',
                'sortBy'    =>  [['l.wn','DESC'],['l.website', 'DIR']],
                'order' =>      'a',
                'sort' =>       'l.website',
                'td_class' =>   'l2',
                'th_class' =>   'l2',
                'tooltip' =>    '',
            ],
            'nwl' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'ndbWebLog',
                'highlight' =>  false,
                'label' =>      'NWL',
                'order' =>      '',
                'sort' =>       '',
                'td_class' =>   '',
                'th_class' =>   '',
                'tooltip' =>    'NDB Weblog for Listener',
            ],
            'map' => [
                'admin' =>      false,
                'arg' =>        '',
                'field' =>      'getSignalsMap',
                'highlight' =>  false,
                'label' =>      'Map',
                'order' =>      '',
                'sort' =>       '',
                'td_class' =>   '',
                'th_class' =>   '',
                'tooltip' =>    'Map showing Signals for Listener',
            ],
            'mapPos' => [
                'admin' =>      true,
                'arg' =>        '',
                'field' =>      'formattedMapPos',
                'highlight' =>  false,
                'label' =>      'Map Pos',
                'order' =>      'a',
                'sort' =>       'l.mapX',
                'td_class' =>   '',
                'th_class' =>   'txt_vertical',
                'tooltip' =>    '',
            ],
            'addlog' => [
                'admin' =>      true,
                'arg' =>        '',
                'field' =>      'addLog',
                'highlight' =>  false,
                'label' =>      'Add Log',
                'order' =>      '',
                'sort' =>       '',
                'td_class' =>   '',
                'th_class' =>   'txt_vertical',
                'tooltip' =>    '',
            ],
            'delete' => [
                'admin' =>      true,
                'arg' =>        '',
                'field' =>      'delete',
                'highlight' =>  false,
                'label' =>      'Delete',
                'order' =>      '',
                'sort' =>       '',
                'td_class' =>   '',
                'th_class' =>   'txt_vertical',
                'tooltip' =>    '',
            ],
        ];
    }
}
