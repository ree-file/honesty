<?php

namespace App\Admin\Controllers\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    public function export()
    {
        Excel::create('店铺入库记录', function($excel) {

            $excel->sheet('first', function($sheet) {
                // 这段逻辑是从表格数据中取出需要导出的字段
                $rows = collect($this->getData())->map(function ($item) {
                    return
                    [
                      $item['supplier']['supplier_name'],
                      $item['goods']['goods_name'],
                      $item['num'],
                      $item['created_at']==null?'第一次':$item['created_at']
                    ];
                });
                $rows = $rows->prepend(['店铺','食品','数量','日期']);
                $sheet->rows($rows);

            });

        })->export('xls');
    }
}
