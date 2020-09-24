<?php
namespace App\Columns;

class Users
{
    public function getColumns()
    {
        return [
            'username' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'username',
                'highlight' =>  'username',
                'label'     =>  'User Name',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'username',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Login Username',
            ],
            'name' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'name',
                'highlight' =>  'name',
                'label'     =>  'Name',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'name',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Name of person account belongs to',
            ],
            'email' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'email',
                'highlight' =>  'email',
                'label'     =>  'email',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'email',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Email address associated with this account',
            ],
            'countLog' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'countLog',
                'highlight' =>  false,
                'label'     =>  'Log Entries',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'countLog',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Number of Log Entries this user has processed',
            ],
            'countLogSession' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'countLogSession',
                'highlight' =>  false,
                'label'     =>  'Log Sessions',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'countLogSession',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Number of Log Sessions this user has processed',
            ],
            'logonCount' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logonCount',
                'highlight' =>  false,
                'label'     =>  'Logon Count',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'logonCount',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Number of times this account has logged in',
            ],
            'logonLatest' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'logonLatest',
                'highlight' =>  false,
                'label'     =>  'Logon Latest',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'logonLatest',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Date on which this account last logged in',
            ],
            'access' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'access',
                'highlight' =>  false,
                'label'     =>  'Roles',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'access',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Administrator Role(s) for this user',
            ],
            'active' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'active',
                'highlight' =>  false,
                'label'     =>  'Active',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'active',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Active users can log in',
            ],
        ];
    }
}