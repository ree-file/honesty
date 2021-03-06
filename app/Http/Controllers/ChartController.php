<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Supplier;
class ChartController extends Controller
{
    //
    public function index(Request $request)
    {

      if ($request->supplier_id==0) {
        $data = $this->createChart($request);
        return $this->success($data);
      }
      else {
        $data = $this->SupplierGoodsChart($request);
        return $this->success($data);
      }

    }
    protected function SupplierGoodsChart($request)
    {
      if ($request->goods_id==0) {
        $data = $this->SupplierHistory($request);
        return $data;
      }
      else {
        $data = $this->SupplierGoodsHistory($request);
        return $data;
      }
    }
    protected function SupplierGoodsHistory($request)
    {
      $order = $this->historyCommon($request);
      $order = $order->map(function($item,$key)use($request){
        $item =$item->map(function($goods,$key)use($request){

            $goodscontent = (array)unserialize($goods['goods']['goods_content']);
            $num = 0;
            for ($i=0; $i < count($goodscontent); $i++) {
                if ($goodscontent[$i]['goods_id']==$request->goods_id) {
                  $num = $goodscontent[$i]['number'];
                  break;
                }
            }
            return ['time'=>$goods['time'],'num'=>$num];
          });
          return [date("Y-m-d",$item[0]['time']),$item->sum('num')];
      });
      return $order->values();
    }
    protected function historyCommon($request)
    {
      if ($request->begin==0) {
        $order = Order::with(['ordergoods'])->where('order_status',3)->where('supplier_id',$request->supplier_id)->orderBy('created_at')->get();
        $firstDay =strtotime($order[0]['created_at']);
        $firstDay = date("Y-m-d",$firstDay);
        $firstDay = strtotime($firstDay);
      }else {
        $begin = date("Y-m-d",$request->begin);
        $end  = date("Y-m-d",$request->end);
        $order = Order::where('order_status',3)->where('supplier_id',$request->supplier_id)->where('created_at','>',$begin)->where('created_at',"<",$end)->orderBy('created_at')->get();
        $firstDay =$request->begin;
      }
      $order = $order->map(function($item,$key)use($firstDay){
        $time = strtotime($item['created_at']);
        $day = floor(($time-$firstDay)/86400);
        return ['time'=>($firstDay+$day*86400),'money'=>$item['order_pay'],'goods'=>$item['ordergoods']];
      });
      $order = $order->groupBy(function($item,$key){
        return 'time_'.$item['time'];
      });
      return $order;
    }
    protected function SupplierHistory($request)
    {
      $order = $this->historyCommon($request);
      $order = $order->map(function($item,$key){
        return [date('Y-m-d',$item[0]['time']),$item->count(),round($item->sum('money'),2)];
      });
      return $order->values();
    }
    protected function createChart($request)
    {
        if ($request->goods_id!=0) {
          $data = $this->goodsChart($request);
          return $data;
        }
        else {
          $data = $this->supplierChart($request);
          return $data;
        }

    }
    protected function goodsChart($request)
    {
      if ($request->begin==0) {
          $data = Order::with(['ordergoods','supplier'])->where('order_status',3)->get();
          $data = $this->supplier_goods($request,$data);


      }
      else{
        $begin = date("Y-m-d",$request->begin);
        $end  = date("Y-m-d",$request->end);
        $data = Order::with(['ordergoods'])->where('order_status',3)->where('created_at',">",$begin)->where('created_at',"<",$end)->get();
        $data = $this->supplier_goods($request,$data);
      }
      return $data;
    }
    protected function supplier_goods($request,$data)
    {
      $data = $data->groupBy(function($item,$key){
        return 'supplier_'.$item['supplier_id'];
      });
      // dd($data->toArray());

      $data = $data->map(function($item,$key) use($request){

          $item =$item->map(function($goods,$key)use($request){

              $goodscontent = (array)unserialize($goods['ordergoods']['goods_content']);
              $num = 0;
              for ($i=0; $i < count($goodscontent); $i++) {
                  if ($goodscontent[$i]['goods_id']==$request->goods_id) {
                    $num = $goodscontent[$i]['number'];
                    break;
                  }
              }
              return ["supplier_name"=>$goods['supplier']['supplier_name'],'num'=>$num];
            });
        // dd($item->toArray());

        $num = $item->sum('num');
        return ["supplier_name"=>$item[0]['supplier_name'],'goods_num'=>$num];
      });
      return $data;
    }
    protected function supplierChart($request)
    {
      if ($request->begin==0) {
        $order = Order::with(['supplier'])->where('order_status',3)->get();
        $data_order = $this->supplierOrder($order);
        $data_honesty = $this->supplierHonesty();
        return ['orderRank'=>$data_order,'honestyRank'=>$data_honesty];
      }
      else {
        $begin = date("Y-m-d",$request->begin);
        $end  = date("Y-m-d",$request->end);
        $data = Order::with(['supplier'])->where('order_status',3)->where('created_at',">",$begin)->where('created_at',"<",$end)->get();
        $data_order = $this->supplierOrder($data);
        $data_honesty = $this->supplierHonesty();
        return ['orderRank'=>$data_order,'honestyRank'=>$data_honesty];
      }
    }
    protected function supplierOrder($order)
    {

      $order = $order->groupBy(function($item,$key){
        return 'supplier_'.$item['supplier_id'];
      });

      $order = $order->map(function($item,$key){
        return [0,$item[0]['supplier']['supplier_name'],$item->count(),$item->sum('order_pay')];
      });
      $order = $order->values()->toArray();
      $orderBycount = $this->Bydesc($order,2);
      $orderBymoney = $this->Bydesc($order,3);
      return ['count'=>$orderBycount,'money'=>$orderBymoney];
    }
    protected function supplierHonesty()
    {
      $supplier = Supplier::orderBy('honesty_rate','desc')->get();
      return $supplier;
    }
}
