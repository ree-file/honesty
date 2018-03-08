<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Goods;
use App\Supplier;
use App\Goodscategory;
use App\Order;
use App\OrderGoods;
use App\Suppliersales;
use DB;
class SupplierController extends Controller
{
    //
    public function index(Request $request)
    {
      $supplier = Supplier::with(['goods'=>function($query){
        $query->select("goods_name","price","img","category_id");
      },"announcements"])->where("id",$request->supplier_id)->get();
      $category = Goodscategory::all();
      $supplier = $this->suppliercontent($supplier,$category);
      return $this->success($supplier);
    }

    public function goods(Request $request)
    {
      $supplier_goods = Supplier::with(['goods'=>function($query){
        $query->select("goods_name");
      }])->where("id",$request->supplier_id)->get();
      $supplier_goods = $this->reconstruction($supplier_goods);
      return $this->success($supplier_goods);
    }

    public function buy(Request $request)
    {
      $order = $this->createOrder($request);
      $new_order = new Order;
      $new_order->fill($order);
      $new_order->save();
      $OrderGoods = new OrderGoods;
      $OrderGoods->order_id = $new_order->id;
      $OrderGoods->goods_content = serialize($order['goods']);
      $OrderGoods->save();
      return $this->success($order['goods']);
      // 创建支付单。
    	// $alipay = app('alipay.web');
    	// $alipay->setOutTradeNo($order['order_code']);
    	// $alipay->setTotalFee($order['order_pay']);
    	// $alipay->setSubject('宅校');
    	// $alipay->setBody('goods_description');
      //
      // $alipay->setQrPayMode('4'); //该设置为可选，添加该参数设置，支持二维码支付。
      //
    	// // // 跳转到支付页面。
    	// return redirect()->to($alipay->getPayLink());
    }
    public function operate(Request $request)
    {
      $operate_record = $this->createrecord($request);
      $Suppliersales = DB::table('suppliersale')->insert($operate_record);
      // $Suppliersales = Suppliersales::create(['supplier_id'=>1,'added'=>1,'leave'=>1,'goods_id'=>1]);
      return $this->success($Suppliersales);
    }
}
