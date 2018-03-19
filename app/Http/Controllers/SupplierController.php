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
use App\Supplierfavorable;
use App\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
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
      if (!$user) {
        $zhaiUser = DB::table("ecs_weixin_user")->where("fake_id",$request->openid)->first();

        $user = new User;
        $user->openid = $zhaiUser->fake_id;
        $user->nickname = $zhaiUser->nickname;
        $user->zhai_id = $zhaiUser->ecuid;
        $user->save();
      }
      $favorable = Supplierfavorable::where("supplier_id",$request->supplier_id)->orderBy('limit', 'desc')->get();
      $order = $this->createOrder($request,$user,$favorable);

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
      $this->CalculateHonesty($request);
      $operate_record = $this->createrecord($request);
      $Suppliersales = DB::table('suppliersale')->insert($operate_record->goods);
      // $Suppliersales = Suppliersales::create(['supplier_id'=>1,'added'=>1,'leave'=>1,'goods_id'=>1]);
      $affact = DB::statement($operate_record->update_goods);

      return $this->success($affact);
    }
    protected function CalculateHonesty($request){
      $goods = collect($request->goods);
      if ($goods->sum('leave')!=0) {
        $income = Order::where('order_status',3)->where('supplier_id',$request->supplier_id)->where('created_at',"<",now())->sum('order_pay');
        $invest = Suppliersales::with(['goods'])->where('supplier_id',$request->supplier_id)->get();
        $invest = $invest->groupBy(function($item,$key){
          return 'goods_'.$item['goods_id'];
        });
        $invest = $invest->map(function($item,$key){
          $true_invest = 0;
          for ($i=0; $i < $item->count()-1; $i++) {
            $true_invest +=($item[$i]['added']+$item[$i]['leave']-$item[$i+1]['leave']);
          }
          $worth = ["num"=>$true_invest,"price"=>$item[0]['goods']['price']];
          return ['worth'=>(floatval($worth['num'])*floatval($worth['price']))];
        });
        $invest = $invest->sum('worth');
        $honesty = round($income/$invest,2);
        $supplier = Supplier::find($request->supplier_id);

        $supplier->honesty_rate = $honesty;
        $supplier->save();
      }
    }
    public function sale(Request $request)
    {
      $result = $this->onsale($request);
      $affact = DB::statement($result['update_goods']);
      DB::statement($result['update_supplier']);
      $log = DB::table('log')->insert($result['output']);
      if ($log&&$affact) {
        return $this->success($affact);
      }
      else {
        return $this->failed("请重新插入");
      }
    }
}
