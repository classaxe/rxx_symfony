<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-09-03
 * Time: 04:44
 */

namespace App\Columns;

class ListenerSignals
{
    public function getColumns()
    {
        return [
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
            'lsb' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'lsb',
                'label'     =>  'LSB',
                'order'     =>  'a',
                'sort'      =>  'l.lsb',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Lower Sideband',
            ],
            'usb' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'usb',
                'label'     =>  'USB',
                'order'     =>  'a',
                'sort'      =>  'l.usb',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Upper Sideband',
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
            'qth' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'qth',
                'label'     =>  'Location',
                'order'     =>  'a',
                'sort'      =>  's.qth',
                'td_class'  =>  'clipped',
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
            'region' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'region',
                'label'     =>  'Region',
                'order'     =>  'a',
                'sort'      =>  's.region',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Region',
            ],
            'gsq' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'gsq',
                'label'     =>  'GSQ',
                'order'     =>  'a',
                'sort'      =>  's.gsq',
                'td_class'  =>  'monospace',
                'th_class'  =>  '',
                'tooltip'   =>  'Maidenhead Locator Grid Square',
            ],
            'pwr' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'pwr',
                'label'     =>  'Power',
                'order'     =>  'a',
                'sort'      =>  's.pwr',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Transmission Power',
            ],
            'logs' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logs',
                'label'     =>  'Logs',
                'order'     =>  'd',
                'sort'      =>  'logs',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'notes' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'notes',
                'label'     =>  'Notes',
                'order'     =>  'a',
                'sort'      =>  's.notes',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'latest' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'latest',
                'label'     =>  'Latest by Listener',
                'order'     =>  'd',
                'sort'      =>  'latest',
                'td_class'  =>  'text-nowrap',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'lastHeard' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'lastHeard',
                'label'     =>  'Last Heard',
                'order'     =>  'd',
                'sort'      =>  'lastHeard',
                'td_class'  =>  'text-nowrap',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'dxKm' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxKm',
                'label'     =>  'KM',
                'order'     =>  'd',
                'sort'      =>  'l.dxKm',
                'td_class'  =>  'txt_r personalise',
                'th_class'  =>  'txt_r ',
                'tooltip'   =>  '',
            ],
            'dxMiles' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxMiles',
                'label'     =>  'Miles',
                'order'     =>  'd',
                'sort'      =>  'l.dxMiles',
                'td_class'  =>  'txt_r personalise',
                'th_class'  =>  'txt_r ',
                'tooltip'   =>  '',
            ],
            'dxDeg' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxDeg',
                'label'     =>  'Deg',
                'order'     =>  'd',
                'sort'      =>  'l.dxDeg',
                'td_class'  =>  'txt_r personalise',
                'th_class'  =>  'txt_r ',
                'tooltip'   =>  '',
            ],
        ];
    }
}
