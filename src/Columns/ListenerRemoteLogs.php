<?php
namespace App\Columns;

class ListenerRemoteLogs
{
    public function getColumns()
    {
        return [
            'id' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'log_id',
                'label'     =>  'Log ID',
                'order'     =>  'a',
                'sort'      =>  'log_id',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'receiver' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'receiver',
                'label'     =>  'Receiver',
                'order'     =>  'a',
                'sort'      =>  'receiver',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
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
                'label'     =>  'UTC',
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
                'td_class'  =>  'txt_r',
                'th_class'  =>  'txt_r',
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
            'lsb' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'lsb',
                'label'     =>  'LSB',
                'order'     =>  'a',
                'sort'      =>  'l.lsb',
                'td_class'  =>  'txt_r',
                'th_class'  =>  'txt_r',
                'tooltip'   =>  'Lower Sideband',
            ],
            'usb' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'usb',
                'label'     =>  'USB',
                'order'     =>  'a',
                'sort'      =>  'l.usb',
                'td_class'  =>  'txt_r',
                'th_class'  =>  'txt_r',
                'tooltip'   =>  'Upper Sideband',
            ],
            'sec' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'secF',
                'label'     =>  'Sec.',
                'order'     =>  'a',
                'sort'      =>  'l.sec',
                'td_class'  =>  'txt_r',
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
            'pwr' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'pwr',
                'label'     =>  'Power',
                'order'     =>  'a',
                'sort'      =>  's.pwr',
                'td_class'  =>  'txt_r',
                'th_class'  =>  'txt_r',
                'tooltip'   =>  'Transmission Power',
            ],
            'dxKm' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxKm',
                'label'     =>  'KM',
                'order'     =>  'd',
                'sort'      =>  'l.dxKm',
                'td_class'  =>  'txt_r',
                'th_class'  =>  'txt_r',
                'tooltip'   =>  '',
            ],
            'dxMiles' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxMiles',
                'label'     =>  'Miles',
                'order'     =>  'd',
                'sort'      =>  'l.dxMiles',
                'td_class'  =>  'txt_r',
                'th_class'  =>  'txt_r',
                'tooltip'   =>  '',
            ],
            'delete' => [
                'admin'     =>  true,
                'arg'       =>  '',
                'field'     =>  'delete',
                'label'     =>  'Delete',
                'order'     =>  '',
                'sort'      =>  '',
                'td_class'  =>  '',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  '',
            ],
        ];
    }
}
