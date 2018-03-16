<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Supplier;
use App\Goods;
use App\Order;
use App\Suppliersales;
use DB;
class CalculateHonesty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CalculateHonesty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算诚信率';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $order = Order::where('order_status',3)->get();
        if ($order->isEmpty()) {
          return 0;
        }
        $order = $order->groupBy(function($item,$key){
          return 'supplier_'.$item['supplier_id'];
        });//将订单按店铺分组

        $order = $order->map(function($item,$key){
          return [$item[0]['supplier_id'],$item->sum('order_pay')];
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
            $worth = ["num"=>($goods_all->sum('added')-$goods_all->sum('leave')),"price"=>$goods_all[0]['goods']['price']];
            return ['worth'=>(floatval($worth['num'])*floatval($worth['price']))];
          });
          return $item;
        });//将每个店铺总的投资记录算出来
        $invest = $invest->map(function($item,$key){
          return ['worth'=>$item->sum('worth')];
        });
        $order = $order->toArray();
        $invest = $invest->toArray();
        $calculate = 'update supplier set honesty_rate = case ';
        $ids = '(';
        foreach ($invest as $key => $value) {
          if (!isset($order[$key][1])) {
              return 0;
              break;
          }
          $one_invest = $value['worth'];
          $honesty_rate = floatval($order[$key][1])/floatval($one_invest);
          $calculate = $calculate." when id = ".$order[$key][0]." then ".round($honesty_rate,2);
          $ids = $ids.$order[$key][0].",";
        }
        $calculate = $calculate." else 0 end where id in ".$ids."0)";
        $affact = DB::statement($calculate);

    }
}
