<?php

namespace App\Admin\Controllers\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    public function export()
    {
        Excel::create('Filename', function($excel) {

            $excel->sheet('Sheetname', function($sheet) {
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
                array_unshift($rows,['店铺','商品','数量','时间']);
                $sheet->rows($rows);

            });

        })->export('xls');
    }
}
