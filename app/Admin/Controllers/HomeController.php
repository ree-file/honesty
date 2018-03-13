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
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Collection;
class HomeController extends Controller
{
  protected $order = "";
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('诚信小铺后台');
            $content->description('网站概要信息');

            $lastSunday = date('Y-m-d', strtotime('-1 monday', time()));
            $order = Order::with(["supplier"])->where('created_at',">",$lastSunday)->where('created_at',"<",now())->get();

            $content->row(function (Row $row) use($order){
              $row->column(3,function (Column $column) use($order){

                $today = date("Y-m-d");
                $count = $order->where("created_at",">",$today)->count();
                $infoBox = new InfoBox('今日订单数', 'chrome', 'primary', '', $count);
                $column->append($infoBox);
              });
              $row->column(3,function(Column $column)use($order){
                $lastSunday = date('Y-m-d', strtotime('-1 monday', time()));

                $count = $order->where('created_at',">",$lastSunday)->where('created_at',"<",now())->count();
                $infoBox = new InfoBox('本周订单数','arrows','aqua',"",$count);
                $column->append($infoBox);
              });
              $row->column(3,function(Column $column)use ($order){
                $today = date("Y-m-d");

                $count = $order->where('created_at',">",$today)->where('order_status',"2")->sum("order_pay");
                $infoBox = new InfoBox('今日收益','chrome','primary',"",$count);
                $column->append($infoBox);
              });
              $row->column(3,function(Column $column)use($order){
                $lastSunday = date('Y-m-d', strtotime('-1 monday', time()));

                $count = $order->where('created_at',">",$lastSunday)->where('created_at',"<",now())->sum("order_pay");
                $infoBox = new InfoBox('本周收益','arrows','aqua',"",$count);
                $column->append($infoBox);
              });
            });
            $content->row(function (Row $row)use($order){
              $row->column(12,function (Column $column)use($order){
                $tab = new Tab();
                $today = date("Y-m-d");
                $today_headers = ['排名','店铺名','数量'];
                $week_headers = ['排名','店铺名','金额'];
                $today_headers_count = collect([]);
                $today_headers_money = collect([]);
                $week_headers_count = collect([]);
                $week_headers_money = collect([]);
                for ($i=0; $i < $order->count(); $i++) {
                  if ($week_headers_count->contains($order[$i]['supplier']['supplier_name'])) {
                    for ($j=0; $j < $week_headers_count->count(); $j++) {
                      if ($week_headers_count[$j][1]==$order[$i]['supplier']['supplier_name']) {
                          $week_headers_count[$j][3] = intval($week_headers_count[$j][3])+1;
                          $week_headers_money[$j][3] = floatval($week_headers_money[$j][3])+floatval($order[$i]['order_pay']);
                      }
                    }
                  }
                  else {
                    $array1=[];
                    $array2=[];
                    $array1 = [$week_headers_count->count(),
                              $order[$i]['supplier']['supplier_name'],
                              1];
                    $array2 = [$week_headers_count->count(),
                            $order[$i]['supplier']['supplier_name'],
                            $order[$i]['order_pay']];
                    $week_headers_count->push($array1);
                    $week_headers_money->push($array2);
                  }
                  if ($order[$i]['created_at']>$today) {
                    if ($today_headers_count->contains($order[$i]['supplier']['supplier_name'])) {
                      for ($j=0; $j < $today_headers_count->count(); $j++) {
                        if ($today_headers_count[$j][1]==$order[$i]['supplier']['supplier_name']) {
                            $today_headers_count[$j][3] = intval($today_headers_count[$j][3])+1;
                            $today_headers_money[$j][3] = floatval($today_headers_money[$j][3])+floatval($order[$i]['order_pay']);
                          }
                        }

                    }
                    else {
                      $array3=[];
                      $array4=[];
                      $array3 = [$today_headers_money->count(),
                                $order[$i]['supplier']['supplier_name'],
                                1];
                      $array4 = [$today_headers_money->count(),
                              $order[$i]['supplier']['supplier_name'],
                              $order[$i]['order_pay']];
                      $today_headers_count->push($array3);
                      $today_headers_money->push($array4);
                    }
                  }

                }
                $tab->add("今天订单排行", new Table($today_headers,$today_headers_count->toArray()));
                $tab->add('本周订单排行', new Table($today_headers,$week_headers_count->toArray()));
                $tab->add("今天收益排行", new Table($week_headers,$today_headers_money->toArray()));
                $tab->add("本周收益排行", new Table($week_headers,$week_headers_money->toArray()));
                $column->append($tab);
              });
            });
        });
    }
    public function FunctionName($value='')
    {
      # code...
    }
}
