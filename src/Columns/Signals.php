<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-09-03
 * Time: 04:44
 */

namespace App\Columns;

class Signals
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
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Frequency',
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
                'tooltip'   =>  'Callsign or other ID',
            ],
            'lsb' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'lsb',
                'label'     =>  'LSB',
                'order'     =>  'a',
                'sort'      =>  's.lsb',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Lower sideband offset',
            ],
            'usb' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'usb',
                'label'     =>  'USB',
                'order'     =>  'a',
                'sort'      =>  's.usb',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  'Upper sideband offset',
            ],
            'qth' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'qth',
                'label'     =>  '\'Name\' and Location',
                'order'     =>  'a',
                'sort'      =>  's.qth',
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
                'sort'      =>  's.sp',
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
                'sort'      =>  's.itu',
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
                'sort'      =>  'i.region',
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
//            'admin' => [
//                'admin'     =>  true,
//                'arg'       =>  '',
//                'field'     =>  'formattedDeleteLink',
//                'label'     =>  'Admin',
//                'order'     =>  '',
//                'sort'      =>  '',
//                'td_class'  =>  '',
//                'th_class'  =>  '',
//                'tooltip'   =>  '',
//            ],
        ];
    }
}
