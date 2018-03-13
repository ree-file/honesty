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
use App\Supplier;
class HomeController extends Controller
{
  protected $order = "";
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('诚信小铺后台');
            $content->description('网站概要信息');

            $lastSunday = date('Y-m-d', strtotime('-1 monday', time()));
            $order = Order::with(["supplier"])->where('created_at',">",$lastSunday)->where('created_at',"<",now())->where('order_status',"2")->get();

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

                $count = $order->where('created_at',">",$today)->sum("order_pay");
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
                $headers = ['排名','店铺名','数量','金额'];
                $data = Supplier::all();
                $week_data = $data->map(function($item,$key)use($order){
                  $supplier_order = $order->where('supplier_id',$item['id']);
                  return [0,$item['supplier_name'],$supplier_order->count(),$supplier_order->sum("order_pay")];
                });
                $today_data = $data->map(function($item,$key)use($order){
                  $today = date("Y-m-d");
                  $supplier_order = $order->where('supplier_id',$item['id'])->where('created_at',">",$today);
                  return [0,$item['supplier_name'],$supplier_order->count(),$supplier_order->sum("order_pay")];
                });
                $today_data_count = $this->Bydesc($today_data->toArray(),2);
                $week_data_count = $this->Bydesc($week_data->toArray(),2);
                $today_data_money = $this->Bydesc($today_data->toArray(),3);
                $week_data_money = $this->Bydesc($week_data->toArray(),3);
                $tab->add("今天订单排行", new Table($headers,$today_data_count));
                $tab->add('本周订单排行', new Table($headers,$week_data_count));
                $tab->add("今天收益排行", new Table($headers,$today_data_money));
                $tab->add("本周收益排行", new Table($headers,$week_data_money));
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
