<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("supplier_id")->unsigned();
            $table->integer("user_id")->unsigned();
            $table->string("order_code");
            $table->decimal("order_pay")
            ->comment("支付金额");
            $table->enum("order_payway",['alipay','weixinpay','balancepay',0])
            ->comment("支付方式");
            $table->enum("order_status",[0,1,2])
            ->comment("订单状态，未确认未支付，确认未支付，确认支付");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}
