<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use App\Order;
class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('诚信小铺后台');
            $content->description('网站概要信息');

            $content->row(function (Row $row){
              $row->column(3,function (Column $column){
                $today = strtotime(date("Y-m-d"),time());
                $count = Order::where('created_at',">",$today)->first();
                $infoBox = new InfoBox('今日订单数', 'chrome', 'primary', '', $count);
                $column->append($infoBox);
              });
              $row->column(3,function(Column $column){
                $lastMonday = strtotime('-2 monday', time());
                $lastSunday = strtotime('-1 monday', time());

                $count = Order::where('created_at',">",$lastMonday)->where('created_at',"<",$lastSunday)->count();
                $infoBox = new InfoBox('上周订单数','arrows','aqua',"",$count);
                $column->append($infoBox);
              });
            });
            $content->row(function (Row $row){


            });
        });
    }
    public function FunctionName($value='')
    {
      # code...
    }
}
