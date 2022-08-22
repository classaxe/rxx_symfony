<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-09-03
 * Time: 04:44
 */

namespace App\Columns;

class SignalListeners
{
    public function getColumns()
    {
        return [
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
            'countLogs' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'countLogs',
                'label'     =>  'Logs',
                'order'     =>  'd',
                'sort'      =>  'COUNT(l.id)',
                'td_class'  =>  'txt_r',
                'th_class'  =>  '',
                'tooltip'   =>  'Total number of logs for this signal',
            ],
            'dxKm' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'dxKm',
                'label'     =>  'KM',
                'order'     =>  'd',
                'sort'      =>  'l.dxKm',
                'td_class'  =>  'txt_r personalise',
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
                'td_class'  =>  'txt_r personalise',
                'th_class'  =>  'txt_r',
                'tooltip'   =>  '',
            ],
        ];
    }
}
