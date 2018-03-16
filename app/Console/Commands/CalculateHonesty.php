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

        $order = $order->groupBy(function($item,$key){
          return 'supplier_'.$item['supplier_id'];
        });//将订单按店铺分组

        $order = $order->map(function($item,$key){
          return [$item[0]['supplier_id'],$item->sum('order_pay')];
        });//计算出每个店铺收益

        $invest = Suppliersales::with(['goods'])->get();

        $invest = $invest->groupBy(function($item,$key){
          return 'supplier_'.$item['supplier_id'];
        });//把投资按店铺分组
        $invest = $invest->map(function($item,$key){
          return [$item[0]['supplier_id'],$item->sum('added'),$item->sum('leave'),$item[0]['goods']['price']];
        });//将每个店铺总的投资记录算出来
        $order = $order->toArray();
        $invest = $invest->toArray();
        $calculate = 'update supplier set honesty_rate = case ';
        $ids = '(';
        foreach ($invest as $key => $value) {
          $one_invest = floatval($value[1]-$value[2])*floatval($value[3]);
          $honesty_rate = floatval($order[$key][1])/floatval($one_invest);
          $calculate = $calculate." when id = ".$value[0]." then ".round($honesty_rate,2);
          $ids = $ids.$value[0].",";
        }
        $calculate = $calculate." else 0 end where id in ".$ids."0)";
        $affact = DB::statement($calculate);

    }
}
