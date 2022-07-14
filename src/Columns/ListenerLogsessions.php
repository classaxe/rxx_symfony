<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-09-03
 * Time: 04:44
 */

namespace App\Columns;

class ListenerLogsessions
{
    public function getColumns()
    {
        return [
            'timestamp' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'timestamp',
                'label'     =>  'Uploaded Date',
                'order'     =>  'd',
                'sort'      =>  'ls.timestamp',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'uploader' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'uploader',
                'label'     =>  'Uploaded By',
                'order'     =>  'a',
                'sort'      =>  'u.name',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'operator' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'operator',
                'label'     =>  'Operator',
                'order'     =>  'a',
                'sort'      =>  'op.name',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'logs' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logs',
                'label'     =>  'Logs',
                'order'     =>  'd',
                'sort'      =>  'ls.logs',
                'td_class'  =>  'txt_r',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Total number of logs for all signals',
            ],
            'logsDgps' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logsDgps',
                'label'     =>  'DGPS',
                'order'     =>  'd',
                'sort'      =>  'ls.logsDgps',
                'td_class'  =>  'txt_r type_dgps',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Number of DGPS logs processed',
            ],
            'logsDsc' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logsDsc',
                'label'     =>  'DSC',
                'order'     =>  'd',
                'sort'      =>  'ls.logsDsc',
                'td_class'  =>  'txt_r type_dsc',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Number of DSC logs processed',
            ],
            'logsHambcn' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logsHambcn',
                'label'     =>  'HAMBCN',
                'order'     =>  'd',
                'sort'      =>  'ls.logsHambcn',
                'td_class'  =>  'txt_r type_hambcn',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Number of Ham Beacon logs processed',
            ],
            'logsNavtex' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logsNavtex',
                'label'     =>  'Navtex',
                'order'     =>  'd',
                'sort'      =>  'ls.logsNavtex',
                'td_class'  =>  'txt_r type_navtex',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Number of NAVTEX station logs processed',
            ],
            'logsNdb' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logsNdb',
                'label'     =>  'NDB',
                'order'     =>  'd',
                'sort'      =>  'ls.logsNdb',
                'td_class'  =>  'txt_r type_ndb',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Number of NDB logs processed',
            ],
            'logsTime' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logsTime',
                'label'     =>  'Time',
                'order'     =>  'd',
                'sort'      =>  'ls.logsTime',
                'td_class'  =>  'txt_r type_time',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Number of Time station logs processed',
            ],
            'logsOther' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logsOther',
                'label'     =>  'Other',
                'order'     =>  'd',
                'sort'      =>  'ls.logsOther',
                'td_class'  =>  'txt_r type_other',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Number of other station logs processed',
            ],
            'firstLog' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'firstLog',
                'label'     =>  'First Log date',
                'order'     =>  'd',
                'sort'      =>  'ls.firstLog',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'lastLog' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'lastLog',
                'label'     =>  'Last Log Date',
                'order'     =>  'd',
                'sort'      =>  'ls.lastLog',
                'td_class'  =>  '',
                'th_class'  =>  '',
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
