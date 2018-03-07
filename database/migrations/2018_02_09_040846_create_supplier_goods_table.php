<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("supplier_id")->unsigned();
            $table->integer("goods_id")->unsigned();
            $table->integer("supplier_num")->comment("商品在小铺中存货");
            $table->integer("shipments")->comment("每日应上货至多少")->default(0);
            $table->decimal("discount")->comment("商品折扣");
            $table->boolean("is_discount")->comment("是否打折");
            $table->date("starttime")->comment("开始日期")->nullable();
            $table->date("deadline")->comment("截止日期")->nullable();
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
        Schema::dropIfExists('supplier_goods');
    }
}
