<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\Goods;
use App\Supplier;
use App\Goodscategory;
use App\Order;
use App\OrderGoods;
use App\Suppliersales;
use Illuminate\Support\Facades\DB;
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
      $user = User::where("openid",$request->openid)->first();
      if ($user) {
        $zhaiUser = DB::table("ecs_weixin_user")->where("fake_id",$request->openid)->first();
        $user = new User;
        $user->openid = $zhaiUser->fake_id;
        $user->nickname = $zhaiUser->nickname;
        $user->zhai_id = $zhaiUser->ecuid;
        $user->save();
      }
      $order = $this->createOrder($request,$user);
      $new_order = new Order;
      $new_order->fill($order);
      $new_order->save();
      $OrderGoods = new OrderGoods;
      $OrderGoods->order_id = $new_order->id;
      $OrderGoods->goods_content = serialize($order['goods']);
      $OrderGoods->save();
      return $this->success($new_order);

    }
    public function operate(Request $request)
    {
      $operate_record = $this->createrecord($request);
      $Suppliersales = DB::table('suppliersale')->insert($operate_record);
      // $Suppliersales = Suppliersales::create(['supplier_id'=>1,'added'=>1,'leave'=>1,'goods_id'=>1]);
      return $this->success($Suppliersales);
    }
}
