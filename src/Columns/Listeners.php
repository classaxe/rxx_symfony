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
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'formattedNameLink',
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
                'td_class'  =>  'txt_r monospace',
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
                'td_class'  =>  'text-nowrap txt_r monospace',
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
            'map' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'formattedSignalsMapLink',
                'label'     =>  'Map',
                'order'     =>  '',
                'sort'      =>  '',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Map showing Signals for Listener',
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
    }
}
