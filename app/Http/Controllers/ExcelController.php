<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use App\Suppliersales;
use Excel;
class ExcelController extends Controller
{
    //
    public function export(Request $request){
        // if ($request->type=="honesty") {
        // $data =   $this->honestyExcel($request);
        // }
        // else {
        //
        // }
        // Excel::create('诚信小铺',function($excel) use ($data){
        //     $excel->sheet('score', function($sheet) use ($data){
        //         $sheet->rows($data);
        //     });
        // })->export('xls');
    }
    protected function honestyExcel($request)
    {
      $order = Order::with(['supplier'])->where('order_status',3)->get();
      if ($order->isEmpty()) {
        return 0;
      }
      $order = $order->groupBy(function($item,$key){
        return 'supplier_'.$item['supplier_id'];
      });//将订单按店铺分组

      $order = $order->map(function($item,$key){
        return [$item[0]['supplier']['supplier_name'],$item->sum('order_pay')];
      });//计算出每个店铺收益

      $invest = Suppliersales::with(['goods'])->get();
      if ($invest->isEmpty()) {
        return 0;
      }
      $invest = $invest->groupBy(function($item,$key){
        return 'supplier_'.$item['supplier_id'];
      });//把投资按店铺分组
      $invest = $invest->map(function($item,$key){
         $item = $item->groupBy(function($goods,$key){
          return 'goods_'.$goods['goods_id'];
        });
        $item=$item->map(function($goods_all,$key){
          // $today = intval($goods_all[$goods_all->count()-1]['added']);
          $worth = ["num"=>($goods_all->sum('added')-$goods_all->sum('leave')),"price"=>$goods_all[0]['goods']['price']];
          return ['worth'=>(floatval($worth['num'])*floatval($worth['price']))];
        });
        return ['worth'=>$item->sum('worth')];
      });//将每个店铺总的投资记录算出来
      $invest = $invest->toArray();
      $order = $order->toArray();
      $header = ['店铺','应收款','实收款','诚信率'];
      $content = [];
      foreach ($invest as $key => $value) {
        $i = 0;
        $content[$i][0] = $order[$key][0];
        $content[$i][1] = $value['worth'];
        $content[$i][2] = $order[$key][1];
        $content[$i][3] = round($order[$key][1]/$value['worth'],2);
        $i++;
      }
      array_unshift($content,$header);
      return $content;
    }
}
