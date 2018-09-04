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
    }
}
