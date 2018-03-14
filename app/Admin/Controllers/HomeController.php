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
use App\OrderGoods;
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
            $order = Order::with(["supplier"=>function($query){
              $query->select("supplier_name");
            }])->where('created_at',">",$lastSunday)->where('created_at',"<",now())->where('order_status',"2")->get();

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
                $goods_header = ['排名','商品名','数量','金额'];
                $data = $this->orderRank($order);
                $goods_data = $this->goodsRank($order);
                $tab->add("今天订单排行", new Table($headers,$data['today_count']));
                $tab->add('本周订单排行', new Table($headers,$data['week_count']));
                $tab->add("今天收益排行", new Table($headers,$data['today_money']));
                $tab->add("本周收益排行", new Table($headers,$data['week_money']));
                $tab->add("本周商品销售数排行", new Table($goods_header,$goods_data['week_goods_count']));
                $tab->add("本周商品销售额排行", new Table($goods_header,$goods_data['week_goods_money']));
                $column->append($tab);
              });
            });
        });
    }
    protected function orderRank($order)
    {
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
      $array = ['today_count'=>$today_data_count,
                'today_money'=>$today_data_money,
                'week_count'=>$week_data_count,
                'week_money'=>$week_data_money
              ];
      return $array;
    }
    protected function goodsRank($order)
    {
      $ids = $order->implode('id',',');
      $ids = explode(',',$ids);
      $ordergoods = OrderGoods::whereIn('order_id',$ids)->get();

      $ordergoods = $ordergoods->map(function($item,$key){
        $goods = unserialize($item['goods_content']);
        return $goods;
      });
      $ordergoods = $ordergoods->collapse();
      $ordergoods = $ordergoods->groupBy(function($item,$key){
        return 'goods_'.$item['goods_id'];
      });

      $ordergoods = $ordergoods->map(function($item,$key){
        return [0,$item[0]['goods_name'],$item->sum('number'),$item->sum('pay')];
      });

      $ordergoods=$ordergoods->values()->toArray();
      $week_goods_count = $this->Bydesc($ordergoods,2);
      $week_goods_money = $this->Bydesc($ordergoods,3);
      return ['week_goods_count'=>$week_goods_count,'week_goods_money'=>$week_goods_money];
    }
}
