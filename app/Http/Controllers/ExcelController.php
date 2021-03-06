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
        if ($request->type=="honesty") {
        $data = $this->honestyExcel($request);
        }
        elseif($request->type=='goods'){

          $data = $this->goodsExcel($request);
        }
        elseif ($request->type=='sale') {
          $data = $this->saleExcel($request);
        }
        Excel::create('诚信小铺',function($excel) use ($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }
    protected function goodsExcel($request)
    {
      if ($request->begin ==0) {
        $goods = Suppliersales::with(['goods','supplier'])->get();
      }
      else {
        $begin = date("Y-m-d",$request->begin);
        $end  = date("Y-m-d",$request->end);
        $goods = Suppliersales::with(['goods','supplier'])->where('created_at',">",$begin)->where('created_at',"<",$end)->get();
      }
      $goods = $goods->groupBy(function($item,$key){
        return 'goods_'.$item['goods_id'];
      });//按商品分组
      $goods = $goods->map(function($item,$key){
        $item = $item->groupBy(function($item,$key){
          return 'supplier_'.$item['supplier_id'];
        });//按店铺分组
        $item = $item->map(function($item,$key){
          $num = 0;
          for ($i=0; $i < $item->count()-1; $i++) {
            $num += (intval($item[$i]['added'])+intval($item[$i]['leave'])-intval($item[$i+1]['leave']));
          }
          return [$item[0]['supplier']['supplier_name'],$item[0]['goods']['goods_name'],$num];
        });//计算某个商品在所有店铺中销售数量
        return $item;
      });
      $goods = $goods->map(function($item,$key){
        return $item->values();
      });
      $goods = $goods->values()->toArray();

      $goodsExcelHeader = ['店铺/食品','1栋','2栋','3栋','4栋','5栋','7栋','8栋','9栋','10栋','11栋','12栋','13栋','14栋','15栋','16栋','17栋','18栋','19栋','20栋'];
      $goods_excel_content = [];
      for ($i=0; $i < count($goods); $i++) {
        $goods_excel_content[$i] = array_fill(0,20,0);
        // dd($goods_excel_content[$i]);
        for ($j=0; $j < count($goods[$i]); $j++) {
          $index = intval($goods[$i][$j][0])>5?intval($goods[$i][$j][0])-1:intval($goods[$i][$j][0]);

          $goods_excel_content[$i][$index] = $goods[$i][$j][2];

        }
        $goods_excel_content[$i][0] = $goods[$i][0][1];
      }
      array_unshift($goods_excel_content,$goodsExcelHeader);
      return $goods_excel_content;
    }
    protected function honestyExcel($request)
    {
      if ($request->begin==0) {
        $order = Order::with(['supplier'])->where('order_status',3)->get();
        $invest = Suppliersales::with(['goods'])->get();
      }
      else {
        $begin = date("Y-m-d",$request->begin);
        $end  = date("Y-m-d",$request->end);
        $order = Order::with(['supplier'])->where('order_status',3)->where('created_at','>',$begin)->where('created_at','<',$end)->get();
        $invest = Suppliersales::with(['goods'])->where('created_at','>',$begin)->where('created_at','<',$end)->get();
      }
      if ($order->isEmpty()) {
        return 0;
      }
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
          $true_invest = 0;
          for ($i=0; $i < $goods_all->count()-1; $i++) {
            $true_invest +=(intval($goods_all[$i]['added'])+intval($goods_all[$i]['leave'])-intval($goods_all[$i+1]['leave']));
          }
          $worth = ["num"=>$true_invest,"price"=>$goods_all[0]['goods']['price']];
          return ['worth'=>($worth['num']*$worth['price']),
          'time'=>$goods_all[$goods_all->count()-1]['created_at']];
        });
        $item = $item->values();
        return ['worth'=>$item->sum('worth'),'time'=>$item[0]['time']];
      });//将每个店铺总的投资记录算出来
      $order = $order->groupBy(function($item,$key){
        return 'supplier_'.$item['supplier_id'];
      });//将订单按店铺分组

      $order = $order->map(function($item,$key)use($invest){
        if (!isset($invest[$key])) {
          return [];
        }
        $item = $item->where('created_at','<',$invest[$key]['time']);
        return [$item[0]['supplier']['supplier_name'],$item->sum('order_pay')];
      });//计算出每个店铺收益

      $invest = $invest->toArray();
      $order = $order->toArray();
      $header = ['店铺','应收款','实收款','诚信率'];
      $content = [];
        $i = 0;
      foreach ($invest as $key => $value) {

        $content[$i][0] = $order[$key][0];
        $content[$i][1] = $value['worth'];
        $content[$i][2] = $order[$key][1];
        $content[$i][3] = $value['worth']==0?0:round($order[$key][1]/$value['worth'],2);
        $i++;
      }
      array_unshift($content,$header);
      return $content;
    }
    protected function saleExcel($request)
    {
      $order = Order::with(['supplier','ordergoods'])->where('order_status',3)->get();
      $order = $order->groupBy(function($item,$key){
        return 'supplier_'.$item['supplier_id'];
      });
      $order = $order->map(function($item,$key){
        $item = $item->map(function($item,$key){

                $goods = (array)unserialize($item['ordergoods']['goods_content']);
                return ['supplier_name'=>$item['supplier']['supplier_name'],'goods'=>$goods];
        });
        return $item;
      });
      $order = $order->values()->toArray();
      $header = [];
      $content =[];
      // dd($order);
      for ($i=0; $i < count($order); $i++) {
        $header[$i] = $order[$i][0]['supplier_name'];
        for ($j=0; $j < count($order[$i]); $j++) {
          $data = $order[$i][$j];
          // dd($data);
          if (!$data['goods']) {
            continue;
          }
          for ($k=0; $k < count($data['goods']); $k++) {
            $goods_data = $data['goods'][$k];
            if (isset($content[$goods_data['goods_id']])) {
              $content[$goods_data['goods_id']][1] +=intval($goods_data['number']);
            }
            else {
              $content[$goods_data['goods_id']] = [$goods_data['goods_name'],intval($goods_data['number'])];
            }
          }
        }
      }
      dd($content);
    }
}
