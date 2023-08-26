<?php
namespace App\Columns;

class Donations
{
    public function getColumns()
    {
        return [
            'id' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'id',
                'highlight' =>  'id',
                'label'     =>  'ID',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'id',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Donor Name',
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
                'tooltip'   =>  'Donor Name',
            ],
            'date' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'date',
                'highlight' =>  'date',
                'label'     =>  'Date',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'date',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Donation Date',
            ],
            'amount' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'amount',
                'highlight' =>  'amount',
                'label'     =>  'Amount',
                'labelSort' =>  '',
                'order'     =>  'a',
                'sort'      =>  'amount',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'rowspan2',
                'tooltip'   =>  'Amount of Donation',
            ],
            'message' => [
                'admin'     =>  false,
                'arg'       =>  '',
                'field'     =>  'message',
                'highlight' =>  'message',
                'label'     =>  'Message',
                'labelSort' =>  '',
                'order'     =>  '',
                'sort'      =>  '',
                'td_class'  =>  'rowspan2',
                'th_class'  =>  'txt_vertical',
                'tooltip'   =>  'Message with Donation',
            ],
        ];
    }
}