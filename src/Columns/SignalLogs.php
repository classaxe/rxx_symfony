<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-09-03
 * Time: 04:44
 */

namespace App\Columns;

class SignalLogs
{
    public function getColumns()
    {
        return [
            'id' => [
                'admin'     =>  true,
                'arg'       =>  '',
                'field'     =>  'log_id',
                'label'     =>  'ID',
                'order'     =>  'a',
                'sort'      =>  'log_id',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'date' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'date',
                'label'     =>  'Date',
                'order'     =>  'd',
                'sort'      =>  'l.date',
                'sort_2'    =>  'l.time',
                'order_2'   =>  'd',
                'td_class'  =>  'text-nowrap',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'time' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'time',
                'label'     =>  'UTC',
                'order'     =>  'a',
                'sort'      =>  'l.time',
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
                'field'     =>  'sec',
                'label'     =>  'Sec.',
                'order'     =>  'a',
                'sort'      =>  'CAST(l.sec AS DECIMAL(10,2))',
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
            'name' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'name',
                'label'     =>  'Name',
                'order'     =>  'a',
                'sort'      =>  'li.name',
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
                'sort'      =>  'li.qth',
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
                'sort'      =>  'li.sp',
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
                'sort'      =>  'li.itu',
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
                'sort'      =>  'li.gsq',
                'td_class'  =>  'monospace',
                'th_class'  =>  '',
                'tooltip'   =>  'Maidenhead Locator Grid Square',
            ],
            'dxKm' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxKm',
                'label'     =>  'KM',
                'order'     =>  'd',
                'sort'      =>  'l.dxKm',
                'td_class'  =>  'txt_r',
                'th_class'  =>  '',
                'tooltip'   =>  'Distance in KM',
            ],
            'dxMiles' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxMiles',
                'label'     =>  'Miles',
                'order'     =>  'd',
                'sort'      =>  'l.dxMiles',
                'td_class'  =>  'txt_r',
                'th_class'  =>  '',
                'tooltip'   =>  'Distance in Miles',
            ],
            'admin' => [
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
