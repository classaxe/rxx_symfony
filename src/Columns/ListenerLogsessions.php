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
            'id' => [
                'admin'     =>  true,
                'arg'       =>  '',
                'field'     =>  'id',
                'label'     =>  'ID',
                'order'     =>  'a',
                'sort'      =>  'li.id',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'timestamp' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'timestamp',
                'label'     =>  'Date',
                'order'     =>  'd',
                'sort'      =>  'ls.timestamp',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'name' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'name',
                'label'     =>  'Admin',
                'order'     =>  'a',
                'sort'      =>  'u.name',
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
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
            ],
            'firstLog' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'firstLog',
                'label'     =>  'First',
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
                'label'     =>  'Last',
                'order'     =>  'd',
                'sort'      =>  'ls.lastLog',
                'td_class'  =>  '',
                'th_class'  =>  '',
                'tooltip'   =>  '',
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
