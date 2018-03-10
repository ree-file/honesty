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
          }
          if ($supplier[0]->goods[$i]->category_id==$category[$j]->id&&$is_live==0) {
            $is_live = 1;
          }
        }
        if ($is_live==1) {
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
      }
      return $data;
    }
    public function createOrder($request,$user)
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
          array_push($check_goods,$one_goods);
          $order_pay += $goods[$i]['number']*$goods[$i]['price'];
        }
      }
      $data = Uuid::uuid1(time());
      $order_code = $data->getHex();
      $order['order_pay'] = $order_pay;
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
      for ($i=0; $i < count($goods); $i++) {
        $goods[$i]['supplier_id'] = $request->supplier_id;
        $goods[$i]['goods_id'] = $goods[$i]['pivot']['goods_id'];
        unset($goods[$i]['pivot']);
        unset($goods[$i]['goods_name']);
      }
      return $goods;
    }
}
