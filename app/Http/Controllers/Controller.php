<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\ApiResponse;
use Ramsey\Uuid\Uuid;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,ApiResponse;

    public function suppliercontent($supplier,$category)
    {
      $remove_idex = [];
      for ($j=0; $j < count($category); $j++) {
        $is_live = 0;
        for ($i=0; $i < count($supplier[0]->goods); $i++) {
          if (!isset($supplier[0]->goods[$i]->number)) {
            $supplier[0]->goods[$i]->number = 0;
            $supplier[0]->goods[$i]->price = round(floatval($supplier[0]->goods[$i]->price)*floatval($supplier[0]->goods[$i]->pivot->discount),2);
          }
          if ($supplier[0]->goods[$i]->category_id==$category[$j]->id&&$is_live==0) {
            $is_live = 1;
          }
        }
        if ($is_live==0) {
          $remove_idex[count($remove_idex)] = $j;
        }
      }
      for ($i=0; $i < count($remove_idex); $i++) {
        unset($category[$remove_idex[$i]]);
      }
       $supplier[0]->category = $category;
       return $supplier;
    }
    public function reconstruction($data)
    {
      for ($i=0; $i < count($data[0]->goods) ; $i++) {
        $data[0]->goods[$i]->leave = 0;
        $data[0]->goods[$i]->added = 0;
        $data[0]->goods[$i]->number = 0;
      }
      return $data;
    }
    public function createOrder($request,$user,$favorable)
    {
      $goods = $request->goods;
      $check_goods = [];
      $order_pay = 0;
      $order = [];
      for ($i=0; $i < count($goods); $i++) {
        if ($goods[$i]['number'] > 0 ) {
          $one_goods = [];
          $one_goods['goods_id']=$goods[$i]['pivot']['goods_id'];
          $one_goods['price']=$goods[$i]['price'];
          $one_goods['number']=$goods[$i]['number'];
          $one_goods['goods_name']=$goods[$i]['goods_name'];
          $one_goods['pay']=round(floatval($goods[$i]['price'])*floatval($goods[$i]['number']),2);
          array_push($check_goods,$one_goods);
          $order_pay += $goods[$i]['number']*$goods[$i]['price'];
        }
      }
      if (!$favorable->isEmpty()) {
        for ($i=0; $i < count($favorable); $i++) {
          if (floatval($favorable[$i]->limit)<=$order_pay) {
            $order_pay -=floatval($favorable[$i]->discountmoney);
            break;
          }
        }
      }
      $data = Uuid::uuid1(time());
      $order_code = $data->getHex();
      $order['order_pay'] = round($order_pay,2);
      $order['goods'] = $check_goods;
      $order['supplier_id'] = $request->supplier_id;
      $order['user_id'] = $user->id;
      $order['order_code'] = $order_code;
      $order['order_payway'] = $request->payway;
      $order['order_status'] = 2;
      return $order;
    }
    public function createrecord($request)
    {
      $goods = $request->goods;
      $update_goods = "update supplier_goods set supplier_num = supplier_num - case ";
      $ids = "(";
      for ($i=0; $i < count($goods); $i++) {
        $goods[$i]['supplier_id'] = $request->supplier_id;
        $goods[$i]['goods_id'] = $goods[$i]['pivot']['goods_id'];
        unset($goods[$i]['pivot']);
        unset($goods[$i]['goods_name']);
        unset($goods[$i]['number']);
        $update_goods = $update_goods." when goods_id = ".$goods[$i]['goods_id']." then ".$goods[$i]['added'];
        $ids = $ids.$goods[$i]['goods_id'].",";
      }
      $update_goods = $update_goods." else 0 end where goods_id in ".$ids."0) and supplier_id = ".$request->supplier_id;
      $request->update_goods = $update_goods;
      $request->goods = $goods;
      return $request;
    }
    public function onsale($request)
    {
      $goods = $request->goods;
      $update_goods = "update goods set num = num - case ";
      $update_supplier = "update supplier_goods set supplier_num = supplier_num + case ";
      $ids = "(";
      $output = [];
      for ($i=0; $i < count($goods); $i++) {
        $update_goods = $update_goods." when id = ".$goods[$i]['pivot']['goods_id']." then ".$goods[$i]['number'];
        $update_supplier = $update_supplier." when goods_id = ".$goods[$i]['pivot']['goods_id']." then ".$goods[$i]['number'];
        $ids = $ids.$goods[$i]['pivot']['goods_id'].",";
        $output[$i]['supplier_id'] = $request->supplier_id;
        $output[$i]['goods_id'] = $goods[$i]['pivot']['goods_id'];
        $output[$i]['num'] = $goods[$i]['number'];
        $output[$i]['created_at']=now();
        $output[$i]['updated_at']=now();
      }
      $update_goods = $update_goods." else 0 end where id in ".$ids."0)";
      $update_supplier = $update_supplier." else 0 end where goods_id in ".$ids."0) and supplier_id = ".$request->supplier_id;
      $result_goods['update_goods'] = $update_goods;
      $result_goods['output'] = $output;
      $result_goods['update_supplier'] = $update_supplier;
      return $result_goods;
    }
    public function Bydesc($data,$key)
    {

      for ($i=0; $i < count($data)-1; $i++) {
        $temp = $data[$i];
        for ($j=$i+1; $j < count($data); $j++) {

          if ($temp[$key]<$data[$j][$key]) {
              $temp = $data[$j];
              $data[$j] = $data[$i];
              $data[$i] = $temp;
          }
        }
        $data[$i][0] = $i+1;
      }
      $data[0][0] = 1;
      $data[count($data)-1][0] = count($data);
      return $data;
    }
}
